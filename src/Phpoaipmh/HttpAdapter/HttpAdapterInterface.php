<?php

namespace Phpoaipmh\HttpAdapter;

/**
 * HttpAdapter HttpAdapterInterface Interface
 *
 * @package Phpoaipmh\HttpAdapter
 */
interface HttpAdapterInterface
{
    /**
     * Perform a GET request to a OAI-PMH endpoint
     *
     * @param string $url The URL string to use
     * @return string  Returns raw, un-parsed XML response body
     * @throws RequestExceptionBase In case of a non 2xx response, or HTTP network error (eg. connect timeout)
     */
    public function request($url);
}

/* EOF: HttpAdapterInterface.php */