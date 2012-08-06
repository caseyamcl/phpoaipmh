<?php

namespace Phpoaipmh\Http;

class Curl implements Client
{
    /**
     * @var int
     * The maximum number of redirects
     */
    protected $maxRedirects = 3;

    /**
     * @var int
     * Connection timeout
     */
    protected $connectTimeout = 10;

    /**
     * @var int
     * DNS lookup timeout
     */
    protected $dnsCacheTimeout = 10;

    /**
     * @var int
     * Response timeout
     */
    protected $responseTimeout = 60;

    // -------------------------------------------------------------------------
    
    /**
     * Constructor
     *
     * Checks for curl libraries
     */
    public function __construct()
    {
        if ( ! is_callable('curl_exec')) {
            throw new \Exception("OAI-PMH Curl HTTP Client requires the CURL PHP Extension");
        }
    }

    // -------------------------------------------------------------------------
    
    /**
     * Do CURL Request
     *
     * @param string $url
     * The full URL 
     *
     * @return string
     * The response body 
     */
    public function request($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, $this->dnsCacheTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->responseTimeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $this->maxRedirects);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP OAI-PMH Library');
        $resp = curl_exec($ch);
        $info = (object) curl_getinfo($ch);
        curl_close($ch);

        //Check response
        $httpCode = (string) $info->http_code;
        if ($httpCode{0} != '2') {
            $msg = sprintf('HTTP Request Failed (code %s): %s', $info->http_code, $resp);
            throw new RequestException($msg);
        }
        elseif (strlen(trim($resp)) == 0) {
            throw new RequestException('HTTP Response Empty');
        }

        return $resp;        
    }
}

/* EOF: Curl.php */