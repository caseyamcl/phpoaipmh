<?php

namespace Phpoaipmh;

class Client
{
    public function __construct() {

        if ( ! is_callable('curl_exec')) {
            throw new \Exception("Phpoaipmh Client requires PHP Curl Extensions");
        }

    }

}

/* EOF: Client.php */
