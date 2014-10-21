<?php

namespace Phpoaipmh\Http;

/**
 * Http Client Interface
 *
 * @package Phpoaipmh\Http
 */
interface Client {

    /**
     * Perform a GET request to a OAI-PMH endpoint
     *
     * @param string $url The URL string to use
     * @throws RequestException In case of a non 2xx response, or HTTP network error (eg. connect timeout)
     */
    public function request($url);
}

/* EOF: Client.php */