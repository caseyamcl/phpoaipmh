<?php

namespace Phpoaipmh;

use Phpoaipmh\Exception\BaseOaipmhException;


/**
 * OAI-PMH Endpoint Class
 *
 * @package Phpoaipmh
 */
class Endpoint
{
    /**
     * @var Client
     */
    private $client;

    // -------------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Client $client  Optional; will attempt to auto-build dependency if not passed
     */
    public function __construct(Client $client = null)
    {
        $this->client = $client ?: new Client();
    }

    // -------------------------------------------------------------------------

    /**
     * Set the URL in the client
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->client->setUrl($url);
    }

    // -------------------------------------------------------------------------

    /**
     * Identify the OAI-PMH Endpoint
     *
     * @return \SimpleXMLElement A XML document with attributes describing the repository
     */
    public function identify()
    {
        $resp = $this->client->request('Identify');
        return $resp;
    }

    // -------------------------------------------------------------------------

    /**
     * List Metadata Formats
     *
     * Return the list of supported metadata format for a particular record (if $identifier
     * is provided), or the entire repository (if no arguments are provided)
     *
     * @param string $identifier If specified, will return only those metadata formats that a particular record supports
     * @param boolean $asResponseList If true, will return a ResponseList object instead of an array
     * @return array|ResponseList An array of SimpleXMLElement objects (or responseList object)
     */
    public function listMetadataFormats($identifier = null, $asResponseList = false)
    {
        $params = ($identifier) ? array('identifier' => $identifier) : array();
        $rList = new ResponseList($this->client, 'ListMetadataFormats', $params);
        return ($asResponseList) ? $rList : $this->processList($rList);
    }

    // -------------------------------------------------------------------------

    /**
     * List Record Sets
     *
     * @param boolean $asResponseList If true, will return a ResponseList object instead of an array
     * @return array|ResponseList An array of SimpleXMLElement objects (or responseList object)
     */
    public function listSets($asResponseList = false)
    {
        $rList = new ResponseList($this->client, 'ListSets');
        return ($asResponseList) ? $rList : $this->processList($rList, 0);
    }

    // -------------------------------------------------------------------------

    /**
     * Get a single record
     *
     * @param string $id Record Identifier
     * @param string $metadataPrefix Required by OAI-PMH endpoint
     * @return \SimpleXMLElement  An XML document corresponding to the record
     */
    public function getRecord($id, $metadataPrefix)
    {
        $params = array(
            'identifier'     => $id,
            'metadataPrefix' => $metadataPrefix
        );

        return $this->client->request('GetRecord', $params);
    }

    // -------------------------------------------------------------------------

    /**
     * List Records
     *
     * Corresponds to OAI Verb to list record identifiers
     *
     * @param string $metadataPrefix Required by OAI-PMH endpoint
     * @param DateTime $from   An optional 'from' date for selective harvesting
     * @param DateTime $until  An optional 'from' date for selective harvesting
     * @param string $set An optional setSpec for selective harvesting
     * @return ResponseList A ResponseList object that encapsulates the records and flow control
     */
    public function listIdentifiers($metadataPrefix, DateTime $from = null, DateTime $until = null, $set = null)
    {
        $params = array('metadataPrefix' => $metadataPrefix);

        if ($from) {
            $params['from'] = $from->format(\DateTime::ISO8601);
        }
        if ($until) {
            $params['until'] = $until>format(\DateTime::ISO8601);
        }
        if ($set) {
            $params['set'] = $set;
        }

        return new ResponseList($this->client, 'ListIdentifiers', $params);
    }

    // -------------------------------------------------------------------------

    /**
     * List Records
     *
     * Corresponds to OAI Verb to list records
     *
     * @param string $metadataPrefix Required by OAI-PMH endpoint
     * @param DateTime $from   An optional 'from' date for selective harvesting
     * @param DateTime $until  An optional 'from' date for selective harvesting
     * @param string $set An optional setSpec for selective harvesting
     * @return ResponseList A ResponseList object that encapsulates the records and flow control
     */
    public function listRecords($metadataPrefix, DateTime $from = null, DateTime $until = null, $set = null)
    {
        $params = array('metadataPrefix' => $metadataPrefix);

        if ($from) {
            $params['from'] = $from->format(\DateTime::ISO8601);
        }
        if ($until) {
            $params['until'] = $until>format(\DateTime::ISO8601);
        }
        if ($set) {
            $params['set'] = $set;
        }
        
        return new ResponseList($this->client, 'ListRecords', $params);
    }

    // -------------------------------------------------------------------------

    /**
     * Convert a ResponseList object into an array
     *
     * Stores the entire array in memory, so this is not particularly useful
     * for listRecords or listIdentifiers, but is useful for small lists,
     * such as you would get back from listSets or listMetadataformats
     *
     * @param ResponseList $rList  A response list object
     * @param int $max  Optional maximum number of records allowed
     * @return array  An array of SimpleXMLElement objects
     * @throws BaseOaipmhException  If response list is greater than the maximum
     */
    public function processList(ResponseList $rList, $max = 500) {

        $outArr = array();

        while ($rec = $rList->nextItem()) {

            if ($max > 0 && $rList->getNumProcessed() >= $max) {
                throw new BaseOaipmhException("Maximum entities reached for responseList!  Set max higher than $max");
            }

            $outArr[] = $rec;
        }

        return $outArr;
    }
}

/* EOF: HttpAdapterInterface.php */