<?php

namespace Phpoaipmh;

/**
 * A simple HTTP Client that performs only GET requests to
 * OAI Endpoints
 */
class Client
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var object
     */
    private $httpClient;

    // -------------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $url
     */
    public function __construct($url, $httpClient = null)
    {
        $this->url = $url;

        if ($httpClient) {
            $this->httpClient = $httpClient;
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Returns a SimpleXMLELement, or throws an exception
     *
     * @return SimpleXMLElement
     */
    public function request($verb, $params = array())
    {
        $resp = $this->doRequest($verb, $params);
        return $this->decodeResponse($resp);
    }

    // -------------------------------------------------------------------------

    /**
     * Strategy method to do a requet.  If an object was passed in the
     * constructor, use that object to do the request.  Otherwise, use CURL
     *
     * @return string
     * The raw response
     */
    private function doRequest($verb, $params = array())
    {
        $params = array_merge(array('verb' => $verb), $params);
        $url = $this->url . '?' . http_build_query($params);

        if ($this->httpClient) {
            return $this->httpClient->request($url);
        }
        else {
            return $this->doCurlRequest($url);
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Do CURL Request
     *
     * @param string $url
     * The full URL 
     * @return string
     */
    private function doCurlRequest($url)
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

    // -------------------------------------------------------------------------

    /**
     * Decode the response into XML
     *
     * @param $resp
     * return SimpleXMLElement
     */
    private function decodeResponse($resp)
    {
        $xml = @new \SimpleXMLElement($resp);

        if ( ! $xml) {
            throw new HttpException("Could not decode the response!");
        }

        //If we get back a OAI-PMH error, throw a OaipmhRequestException
        if (isset($xml->error)) {
            $code = (string) $xml->error['code'];
            $msg  = (string) $xml->error;

            throw new OaipmhReqeustException($code, $msg);
        }

        return $xml;
    }
}

/* EOF: OaipmhClient.php */