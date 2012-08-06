<?php

namespace Phpoaipmh;

class OAIEndpoint
{
    /**
     * @var string
     */
    private $url;

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
    public function __construct($url, Client $client = null)
    {
        $this->url = $url;
        $this->client = $client;
    }

    // -------------------------------------------------------------------------

    public function identify()
    {
        //returns an object
    }

    // -------------------------------------------------------------------------

    public function listMetadataFormats($identifier = null)
    {
        //returns an iterator
    }

    // -------------------------------------------------------------------------

    public function listSets()
    {
        //returns an iterator
    }

    // -------------------------------------------------------------------------

    public function listIdentifiers()
    {
        //returns an iterator
    }

    // -------------------------------------------------------------------------

    public function listRecords($metadataPrefix)
    {
        //returns an iterator
    }

    // -------------------------------------------------------------------------

    public function getRecord($id = null)
    {
        //returns an object
    }

}

/* EOF: Client.php */
