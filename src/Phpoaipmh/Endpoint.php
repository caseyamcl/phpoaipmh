<?php

namespace Phpoaipmh;

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
     * @param string $url
     * @param Client $client
     */
    public function __construct($url = null, Client $client = null)
    {
        $this->client = $client ?: new Client($url);
    }

    // -------------------------------------------------------------------------

    /**
     * Set the URL in the client
     *
     * @param string $url
     */
    public function setUrl()
    {
        $this->client->setUrl($url);
    }

    // -------------------------------------------------------------------------

    /**
     * Identify the OAI-PMH Endpoint
     *
     * @return SimpleXMLElement
     * A XML document with attributes describing the repository
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
     * @param string $identifier
     * If specified, will return only those metadata formats that a particular record supports
     *
     * @param boolean $asResponseList
     * If true, will return a ResponseList object instead of an array
     *
     * @return array|ResponseList
     * An array of SimpleXMLElement objects (or responseList object)
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
     * @param boolean $asResponseList
     * If true, will return a ResponseList object instead of an array
     *
     * @return array|ResponseList
     * An array of SimpleXMLElement objects (or responseList object)
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
     * @param string $id
     * Record Identifier
     *
     * @param string $metadataPrefix
     * Required by OAI-PMH endpoint
     *
     * @return SimpleXMLElement
     * An XML document corresponding to the record
     */
    public function getRecord($id, $metadataPrefix)
    {
        $params = array(
            'identifier'     => $id,
            'metadataPrefix' => $metadataPrefix
        );

        $resp = $this->client->request('GetRecord');
    }

    // -------------------------------------------------------------------------

    /**
     * List Records
     *
     * Corresponds to OAI Verb to list record identifiers
     *
     * @param string $metadataPrefix
     * Required by OAI-PMH endpoint
     *
     * @param string $from
     * An optional ISO8601 encoded date for selective harvesting
     *
     * @param string $to
     * An optional ISO8601 encoded date for selective harvesting
     * 
     * @param string $set
     * An optional setSpec for selective harvesting
     *
     * @return ResponseList
     * A ResponseList object that encapsulates the records and flow control
     */
    public function listIdentifiers($metadataPrefix, $from = null, $until = null, $set = null)
    {
        $params = array('metadataPrefix' => $metadataPrefix);
        if ($from) {
            $params['from'] = $from;
        }
        if ($until) {
            $params['until'] = $until;
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
     * @param string $metadataPrefix
     * Required by OAI-PMH endpoint
     *
     * @param string $from
     * An optional ISO8601 encoded date for selective harvesting
     *
     * @param string $to
     * An optional ISO8601 encoded date for selective harvesting
     * 
     * @param string $set
     * An optional setSpec for selective harvesting
     *
     * @return ResponseList
     * A ResponseList object that encapsulates the records and flow control
     */
    public function listRecords($metadataPrefix, $from = null, $until = null, $set = null)
    {
        $params = array('metadataPrefix' => $metadataPrefix);
        if ($from) {
            $params['from'] = $from;
        }
        if ($until) {
            $params['until'] = $until;
        }
        if ($set) {
            $params['set'] = $set;
        }
        
        return new ResponseList($this->client, 'ListRecords', $params);
    }

    // -------------------------------------------------------------------------

    /** 
     * Convert a ResponseList cursor object into an array
     *
     * Stores the entire array in memory, so this is not particularly useful
     * for listRecords or listIdentifiers, but is useful for small lists,
     * such as you would get back from listSets or listMetadataformats
     *
     * @param ResponseList $rList
     * A response list object
     *
     * @param int $max
     * Optional maximum number to process (default: 200). Set to 0 for unlimited
     *
     * @return array
     * An array of SimpleXMLElement objects
     */
    public function processList(ResponseList $rList, $max = 200) {

        $outArr = array();

        while ($rec = $rList->nextItem()) {

            if ($max > 0 && $rList->getNumProcessed() >= $max) {
                throw new \Exception("Maximum entities reached for responseList!  Set max higher than $max");
            }

            $outArr[] = $rec;
        }

        return $outArr;
    }
}

/* EOF: Client.php */