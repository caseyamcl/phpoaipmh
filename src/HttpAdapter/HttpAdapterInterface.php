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
     * @param  string $url          The URL string to use
     * @param  array  $queryParams  The query parameters to send
     * @return string Returns raw, un-parsed XML response body
     */
    public function request($url, array $queryParams = []);
}
