<?php

namespace Phpoaipmh;

class Client
{
    private $endpoint;

    public function __construct($endpoint) {

        if ( ! is_callable('curl_exec')) {
            throw new \Exception("Phpoaipmh Client requires PHP Curl Extensions");
        }

        $this->endpoint = $endpoint;
    }

    // -------------------------------------------------------------------------

    public function request($params = array()) {

        //Build query parameters
        $params = http_build_query($params);

        var_dump($params);
    } 

    // -------------------------------------------------------------------------

    public function identify() {

    }

    public function listMetadataFormats() {

    }

    public function listSets() {

    }

    public function listIdentifiers() {

    }

    public function listRecords() {

    }

    public function getRecord($id = null) {

    }

}

/* EOF: Client.php */
