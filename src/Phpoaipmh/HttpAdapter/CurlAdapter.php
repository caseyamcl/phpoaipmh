<?php

namespace Phpoaipmh\HttpAdapter;

/**
 * CurlAdapter HttpAdapter HttpAdapterInterface Adapter
 *
 * @package Phpoaipmh\HttpAdapter
 */
class CurlAdapter implements HttpAdapterInterface
{
    /**
     * @var array  CURL Options
     */
    private $curlOpts = [
        CURLOPT_RETURNTRANSFER    => true,
        CURLOPT_CONNECTTIMEOUT    => 10,
        CURLOPT_DNS_CACHE_TIMEOUT => 10,
        CURLOPT_TIMEOUT           => 60,
        CURLOPT_FOLLOWLOCATION    => true,
        CURLOPT_MAXREDIRS         => 3,
        CURLOPT_USERAGENT         => 'PHP OAI-PMH Library',
    ];

    /**
     * Constructor
     *
     * Checks for CURL libraries
     *
     * @param array $curlOpts  Array of CURL directives and values (e.g. [CURLOPT_TIMEOUT => 120])
     * @throws \Exception  If CURL not installed.
     */
    public function __construct(array $curlOpts = [])
    {
        if (! is_callable('curl_exec')) {
            throw new \Exception("OAI-PMH CurlAdapter HTTP HttpAdapterInterface requires the CURL PHP Extension");
        }

        $this->curlOpts = array_replace($this->curlOpts, $curlOpts);
    }

    /**
     * Do CURL Request
     *
     * @param  string $url The full URL
     * @param array   $queryParams
     * @return string The response body
     */
    public function request($url, array $queryParams = [])
    {
        // Add query parameters to URL
        $url = $url . (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . http_build_query($queryParams);

        // Merge URL into curl options
        $curlOpts = array_replace($this->curlOpts, [CURLOPT_URL => $url]);

        $ch = curl_init();
        foreach ($curlOpts as $opt => $optVal) {
            curl_setopt($ch, $opt, $optVal);
        }

        // Do the request

        $resp = curl_exec($ch);
        $info = (object) curl_getinfo($ch);
        curl_close($ch);

        // Basic check response
        if ($resp === false) {
            throw new CurlHttpException(sprintf(
                'HTTP request error (cURL error: %s)',
                curl_error($ch)
            ));
        }
        if (strlen(trim($resp)) == 0) {
            throw new CurlHttpException(sprintf(
                'Empty response body (HTTP code: %s)',
                (string) $info->http_code
            ));
        }

        return $resp;
    }
}
