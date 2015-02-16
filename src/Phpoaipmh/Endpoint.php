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

/**
 * OAI-PMH Endpoint Class
 *
 * @since v1.0
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Endpoint
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $granularity;

    // -------------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Client $client Optional; will attempt to auto-build dependency if not passed
     * @param string $granularity Optional; the OAI date format for fetching records, use constants from Granularity class
     */
    public function __construct(Client $client = null, $granularity = null)
    {
        $this->client = $client ?: new Client();
        $this->granularity = $granularity ? $granularity : $this->fetchGranularity();
    }

    /**
     * Load date format from Identify
     *
     * @return string
     */
    private function fetchGranularity() {
        $response = $this->identify();
        if (isset($response->Identify->granularity)) {
            $this->granularity = $response->Identify->granularity;
        } else {
            $this->granularity = Granularity::DATE; // Default
        }
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
     * @param  string         $identifier If specified, will return only those metadata formats that a particular record supports
     * @return RecordIterator
     */
    public function listMetadataFormats($identifier = null)
    {
        $params = ($identifier) ? array('identifier' => $identifier) : array();

        return new RecordIterator($this->client, 'ListMetadataFormats', $params);
    }

    // -------------------------------------------------------------------------

    /**
     * List Record Sets
     *
     * @return RecordIterator
     */
    public function listSets()
    {
        return new RecordIterator($this->client, 'ListSets');
    }

    // -------------------------------------------------------------------------

    /**
     * Get a single record
     *
     * @param  string            $id             Record Identifier
     * @param  string            $metadataPrefix Required by OAI-PMH endpoint
     * @return \SimpleXMLElement An XML document corresponding to the record
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
     * @param  string         $metadataPrefix Required by OAI-PMH endpoint
     * @param  \DateTime       $from           An optional 'from' date for selective harvesting
     * @param  \DateTime       $until          An optional 'from' date for selective harvesting
     * @param  string         $set            An optional setSpec for selective harvesting
     * @return RecordIterator
     */
    public function listIdentifiers($metadataPrefix, $from = null, $until = null, $set = null)
    {
        $params = array('metadataPrefix' => $metadataPrefix);

        if ($from instanceof \DateTime) {
            $params['from'] = Granularity::formatDate($from, $this->granularity);
        }
        if ($until instanceof \DateTime) {
            $params['until'] = Granularity::formatDate($until, $this->granularity);
        }
        if ($set) {
            $params['set'] = $set;
        }

        return new RecordIterator($this->client, 'ListIdentifiers', $params);
    }

    // -------------------------------------------------------------------------

    /**
     * List Records
     *
     * Corresponds to OAI Verb to list records
     *
     * @param  string         $metadataPrefix Required by OAI-PMH endpoint
     * @param  \DateTime       $from           An optional 'from' date for selective harvesting
     * @param  \DateTime       $until          An optional 'from' date for selective harvesting
     * @param  string         $set            An optional setSpec for selective harvesting
     * @return RecordIterator
     */
    public function listRecords($metadataPrefix, $from = null, $until = null, $set = null)
    {
        $params = array('metadataPrefix' => $metadataPrefix);

        if ($from instanceof \DateTime) {
            $params['from'] = Granularity::formatDate($from, $this->granularity);
        }
        if ($until instanceof \DateTime) {
            $params['until'] = Granularity::formatDate($until, $this->granularity);
        }
        if ($set) {
            $params['set'] = $set;
        }

        return new RecordIterator($this->client, 'ListRecords', $params);
    }
}

/* EOF: Endpoint.php */
