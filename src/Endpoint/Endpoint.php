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

namespace Phpoaipmh\Endpoint;

use Phpoaipmh\Client;
use Phpoaipmh\ClientInterface;
use Phpoaipmh\Model\RequestParameters;

/**
 * OAI-PMH Endpoint Class
 *
 * @since v1.0
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Endpoint implements EndpointInterface
{
    const AUTO = null;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $url;

    /**
     * Constructor
     *
     * @param string          $url     Endpoint URL
     * @param ClientInterface $client  Client instance
     */
    public function __construct($url, ClientInterface $client)
    {
        $this->url    = $url;
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return Client|ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Identify the OAI-PMH Endpoint
     *
     * @return EndpointRecordRequest   A request to get a single record
     */
    public function identify()
    {
        return new EndpointRecordRequest($this->client, new RequestParameters(
            $this->url,
            'Identify'
        ));
    }

    /**
     * List Metadata Formats
     *
     * Return the list of supported metadata format for a particular record (if $identifier
     * is provided), or the entire repository (if no arguments are provided)
     *
     * @param  string         $identifier If specified, will return only those metadata formats that a particular record supports
     * @return EndpointIteratorRequest
     */
    public function listMetadataFormats($identifier = '')
    {
        $params = ($identifier) ? ['identifier' => $identifier] : array();

        return new EndpointIteratorRequest($this->client, new RequestParameters(
            $this->url,
            'ListMetadataFormats',
            $params
        ));
    }

    /**
     * List Record Sets
     *
     * @return EndpointIteratorRequest
     */
    public function listSets()
    {
        return new EndpointIteratorRequest($this->client, new RequestParameters(
            $this->url,
            'ListSets'
        ));
    }

    /**
     * Get a single record
     *
     * @param  string  $id             Record Identifier
     * @param  string  $metadataPrefix Required by OAI-PMH endpoint
     * @return EndpointRecordRequest
     */
    public function getRecord($id, $metadataPrefix)
    {
        return new EndpointRecordRequest($this->client, new RequestParameters(
            $this->url,
            'GetRecord',
            [
                'identifier'     => $id,
                'metadataPrefix' => $metadataPrefix
            ]
        ));
    }

    /**
     * List Record identifiers
     *
     * Corresponds to OAI Verb to list record identifiers
     *
     * @param  string         $metadataPrefix Required by OAI-PMH endpoint
     * @param  \DateTime      $from             An optional 'from' date for selective harvesting
     * @param  \DateTime      $until            An optional 'until' date for selective harvesting
     * @param  string         $set              An optional setSpec for selective harvesting
     * @param  string         $resumptionToken  An optional resumptionToken for selective harvesting
     * @return EndpointIteratorRequest
     */
    public function listIdentifiers($metadataPrefix, \DateTime $from = null, \DateTime $until = null, $set = '', $resumptionToken = '')
    {
        $dateGranularity = $this->client->getDateGranularity($this->url);

        $params = array_filter([
            'metadataPrefix'  => $metadataPrefix,
            'from'            => $from  ? $dateGranularity->formatDate($from)  : null,
            'until'           => $until ? $dateGranularity->formatDate($until) : null,
            'set'             => $set,
            'resumptionToken' => $resumptionToken
        ]);

        return new EndpointIteratorRequest($this->client, new RequestParameters(
            $this->url,
            'ListIdentifiers',
            $params
        ));
    }

    /**
     * List Records
     *
     * Corresponds to OAI Verb to list records
     *
     * @param  string         $metadataPrefix Required by OAI-PMH endpoint
     * @param  \DateTime      $from             An optional 'from' date for selective harvesting
     * @param  \DateTime      $until            An optional 'from' date for selective harvesting
     * @param  string         $set              An optional setSpec for selective harvesting
     * @param  string         $resumptionToken  An optional resumptionToken for selective harvesting
     * @return EndpointIteratorRequest
     */
    public function listRecords($metadataPrefix, \DateTime $from = null, \DateTime $until = null, $set = '', $resumptionToken = '')
    {
        $dateGranularity = $this->client->getDateGranularity($this->url);

        $params = array_filter([
            'metadataPrefix'  => $metadataPrefix,
            'from'            => $from  ? $dateGranularity->formatDate($from)  : null,
            'until'           => $until ? $dateGranularity->formatDate($until) : null,
            'set'             => $set,
            'resumptionToken' => $resumptionToken
        ]);

        return new EndpointIteratorRequest($this->client, new RequestParameters(
            $this->url,
            'ListRecords',
            $params
        ));
    }
}
