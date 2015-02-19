<?php

/**
 * PHPOAIPMH Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/phpoaipmh
 * @version 2.0
 * @package caseyamcl/phpoaipmh
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace Phpoaipmh;

use Phpoaipmh\Exception\BaseOaipmhException;
use Phpoaipmh\Exception\MalformedResponseException;

/**
 * Response List Entity iterates over records returned from an OAI-PMH Endpoint
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @since v2.0
 */
class RecordIterator implements \Iterator
{
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string  The verb to use
     */
    private $verb;

    /**
     * @var array  OAI-PMH parameters passed as part of the request
     */
    private $params;

    /**
     * @var int  The number of total entities (if available)
     */
    private $totalRecordsInCollection;

    /**
     * @var \DateTime  Recordset expiration date (if specified)
     */
    private $expireDate;

    /**
     * @var string  The resumption token
     */
    private $resumptionToken;

    /**
     * @var array  Array of records
     */
    private $batch;

    /**
     * @var int Total number of records processed
     */
    private $numProcessed = 0;

    /**
     * @var boolean  Total number of requests made
     */
    private $numRequests = 0;

    /**
     * @var \SimpleXMLElement|null  Used for tracking the iterator
     */
    private $currItem;

    // -------------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Client $httpClient The client to use
     * @param string $verb       The verb to use when retrieving results from the client
     * @param array  $params     Optional parameters passed to OAI-PMH
     */
    public function __construct(Client $httpClient, $verb, array $params = array())
    {
        //Set parameters
        $this->httpClient = $httpClient;
        $this->verb       = $verb;
        $this->params     = $params;

        //Node name error?
        if (! $this->getItemNodeName()) {
            throw new BaseOaipmhException('Cannot determine item name for verb: ' . $this->verb);
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Get the total number of requests made during this run
     *
     * @return int The number of HTTP requests made
     */
    public function getNumRequests()
    {
        return $this->numRequests;
    }

    // -------------------------------------------------------------------------

    /**
     * Get the total number of records processed during this run
     *
     * @return int The number of records processed
     */
    public function getNumRetrieved()
    {
        return $this->numProcessed;
    }

    // -------------------------------------------------------------------------

    /**
     * Get the total number of records in the collection if available
     *
     * This only returns a value if the OAI-PMH server provides this information
     * in the response, which not all servers do (it is optional in the OAI-PMH spec)
     *
     * Also, the number of records may change during the requests, so it should
     * be treated as an estimate
     *
     * @return int|null
     */
    public function getTotalRecordsInCollection()
    {
        if ($this->currItem === null) {
            $this->next();
        }

        return $this->totalRecordsInCollection;
    }

    // -------------------------------------------------------------------------

    /**
     * Get the next item
     *
     * Return an item from the currently-retrieved batch, get next batch and
     * return first record from it, or return false if no more records
     *
     * @return \SimpleXMLElement|boolean
     */
    public function nextItem()
    {
        //If no items in batch, and we have a resumptionToken or need to make initial request...
        if (count($this->batch) == 0 && ($this->resumptionToken or $this->numRequests == 0)) {
            $this->retrieveBatch();
        }

        //if still items in current batch, return one
        if (count($this->batch) > 0) {
            $this->numProcessed++;

            $item = array_shift($this->batch);
            $this->currItem = new \SimpleXMLElement($item->asXML());
        } else {
            $this->currItem = false;
        }

        return $this->currItem;
    }

    // -------------------------------------------------------------------------

    /**
     * Do a request to get the next batch of items
     *
     * @return int The number of items in the batch after the retrieve
     */
    private function retrieveBatch()
    {
        // Set OAI-PMH parameters for request
        // If resumptionToken, then we ignore params and just use that
        $params = ($this->resumptionToken)
            ? ['resumptionToken' => $this->resumptionToken]
            : $this->params;

        // Node name and verb
        $nodeName = $this->getItemNodeName();
        $verb = $this->verb;

        //Do it..
        $resp = $this->httpClient->request($verb, $params);
        $this->numRequests++;

        //Result format error?
        if (! isset($resp->$verb->$nodeName)) {
            throw new MalformedResponseException(sprintf("Expected XML element list '%s' missing for verb '%s'", $nodeName, $verb));
        }

        //Process the results
        foreach ($resp->$verb->$nodeName as $node) {
            $this->batch[] = $node;
        }

        //Set the resumption token and expiration date, if specified in the response
        if (isset($resp->$verb->resumptionToken)) {
            $this->resumptionToken = (string) $resp->$verb->resumptionToken;

            if (isset($resp->$verb->resumptionToken['completeListSize'])) {
                $this->totalRecordsInCollection = (int) $resp->$verb->resumptionToken['completeListSize'];
            }
            if (isset($resp->$verb->resumptionToken['expirationDate'])) {
                $t = $resp->$verb->resumptionToken['expirationDate'];
                $this->expireDate = \DateTime::createFromFormat(\DateTime::ISO8601, $t);
            }
        } else {
            //Unset the resumption token when we're at the end of the list
            $this->resumptionToken = null;
        }

        //Return a count
        return count($this->batch);
    }

    // -------------------------------------------------------------------------

    /**
     * Get Item Node Name
     *
     * Map the item node name based on the verb
     *
     * @return string|boolean The element name for the mapping, or false if unmapped
     */
    private function getItemNodeName()
    {
        $mappings = array(
            'ListMetadataFormats' => 'metadataFormat',
            'ListSets'            => 'set',
            'ListIdentifiers'     => 'header',
            'ListRecords'         => 'record'
        );

        return (isset($mappings[$this->verb])) ? $mappings[$this->verb] : false;
    }

    // ----------------------------------------------------------------

    /**
     * Reset the request state
     */
    public function reset()
    {
        $this->numRequests  = 0;
        $this->numProcessed = 0;

        $this->currItem                 = null;
        $this->resumptionToken          = null;
        $this->totalRecordsInCollection = null;
        $this->expireDate               = null;

        $this->batch = [];
    }

    // ----------------------------------------------------------------

    public function current()
    {
        return ($this->currItem === null)
            ? $this->nextItem()
            : $this->currItem;
    }

    public function next()
    {
        return $this->nextItem();
    }

    public function key()
    {
        if ($this->currItem === null) {
            $this->nextItem();
        }

        return $this->getNumRetrieved();
    }

    public function valid()
    {
        return ($this->currItem !== false);
    }

    public function rewind()
    {
        $this->reset();
    }
}
