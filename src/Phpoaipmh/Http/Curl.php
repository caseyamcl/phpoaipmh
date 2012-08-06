<?php

namespace Phpoaipmh\Http;

class Curl implements Client
{
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
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP OAI-PMH Library');
        $resp = curl_exec($ch);
        $info = (object) curl_getinfo($ch);
        curl_close($ch);

        //Check response
        $httpCode = (string) $info->http_code;
        if ($httpCode{0} != '2') {
            $msg = sprintf('HTTP Request Failed (code %s): %s', $info->http_code, $resp);
            throw new HttpException($msg);
        }
        elseif (strlen(trim($resp)) == 0) {
            throw new HttpException('HTTP Response Empty');
        }

        return $resp;        
    }
}

/* EOF: Curl.php */