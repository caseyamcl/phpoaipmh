<?php

namespace Phpoaipmh;

class ResponseList {

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $verb;

    /**
     * @var array
     */
    private $params;

    /**
     * @var int
     */
    private $totalEntities;

    /**
     * Recordset expiration date, converted to Unixtime
     *
     * @var int
     */
    private $expireDate;

    /** 
     * @var string
     */
    private $resumptionToken;

    /**
     * @var int
     */
    private $limit = 0;

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * Array of records
     *
     * @var array
     */
    private $batch;

    /**
     * Total processed
     *
     * @var int
     */
    private $totalProcessed;

    // -------------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Client $httpClient
     * @param string $verb
     * @param int $offset
     * @param int $limit
     */
    public function __construct(Client $httpClient, $verb, $params = array(), $offset = 0, $limit = 0)
    {
        if (substr($verb, 0, 4) != 'List') {
            throw new \Exception("Cannot iterate over non-list OAI-PMH requests");
        }

        //Set paramaters
        $this->httpClient = $httpClient;
        $this->verb   = $verb;
        $this->params = $params;
        $this->offset = $offset;
        $this->limit  = $limit;

        //Get first batch - Must happen in constructor
        $this->retrieveBatch();
    }

    // -------------------------------------------------------------------------

    /**
     * Get the next item
     *
     * @return boolean|SimpleXMLElement
     */
    public function nextItem()
    {
        //Determine if we have another item, and possibly
        //do a request to get more

        //If there is a limit and we are at it, return false
        if ($this->limit && $this->totalProcessed >= $this->limit) {
            $item = false;
        }
        //Elseif there are more items, return one of those
        elseif (count($this->batch) > 0) {
            $item = array_shift($this->batch);
        }
        //Elseif we have zero more items and the ability to get more items, do so and try to return one
        elseif ($this->resumptionToken) {
            $this->retrieveBatch();
            $item = (count($this->batch) > 0) ? array_shift($this->batch) : false;
        } 
        //Else, give up..
        else {
            $item = false;
        }

        //If we actually have a record
        if ($item) {
            $this->totalProcessed++;
        }

        //Return it
        return $item;
    }

    // -------------------------------------------------------------------------

    /**
     * Do a request to get the next batch of items
     *
     * @return int
     * The number of items in the batch after the retrieve
     */
    private function retrieveBatch() {

        //Params
        $params = ($this->resumptionToken)
            ? array('resumptionToken' => $this->resumptionToken)
            : $this->params;        
        $nodeName = $this->getItemNodeName();
        $verb = $this->verb;

        //Node name error?
        if ( ! $nodeName) {
            throw new \RuntimeException('Cannot determine item name for verb: ' . $this->verb);
        }

        //Do it..
        $resp = $this->httpClient->request($verb, $params); 
       
        //Result format error?
        if ( ! isset($resp->$verb->$nodeName)) {
            throw new OaipmhReqeustException(sprintf("Expected XML element list %s missing for verb %s"), $nodeName, $verb);
        }

        //Process the results
        foreach($resp->$verb->$nodeName as $node) {
            $this->batch[] = $node;
        }

        //Set the resumption token and expiration date, if any
        if (isset($resp->$verb->resumptionToken)) {
            $this->resumptionToken = (string) $resp->$verb->resumptionToken;

            if (isset($resp->$verb->resumptionToken['completeListSize'])) {
                $this->totalEntities = (int) $resp->$verb->resumptionToken['completeListSize'];
            }
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
     * @return string|boolean
     * The element name for the mapping, or false if unmapped
     */
    private function getItemNodeName() {

        $mappings = array(
            'ListMetadataFormats' => 'metadataFormat',
            'ListSets'            => 'set',
            'ListIdentifiers'     => 'header',
            'ListRecords'         => 'record'
        );

        return (isset($mappings[$this->verb])) ? $mappings[$this->verb] : false;
    }
}

/* EOF: ClientRecordIterator.php */