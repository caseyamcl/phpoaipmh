<?php

namespace Phpoaipmh;

class OAIEndpoint
{
    private $url;

    // -------------------------------------------------------------------------

    public function __construct($url) {

        $this->url = $url;
    }

    // -------------------------------------------------------------------------

    public function identify() {

        //returns an object
    }

    // -------------------------------------------------------------------------

    public function listMetadataFormats($identifier = null) {

        //returns an iterator
    }

    // -------------------------------------------------------------------------

    public function listSets() {

        //returns an iterator
    }

    // -------------------------------------------------------------------------

    public function listIdentifiers() {

        //returns an iterator
    }

    // -------------------------------------------------------------------------

    public function listRecords($metadataPrefix) {
        //returns an iterator
    }

    // -------------------------------------------------------------------------

    public function getRecord($id = null) {
        //returns an object
    }

}

/* EOF: Client.php */
