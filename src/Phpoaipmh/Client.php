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
     * The URL of the OAI-PMH Endpoint
     *
     * @param Http\Client $httpClient
     * Optional HTTP Client class
     *
     */
    public function __construct($url, Http\Client $httpClient = null)
    {
        $this->url = $url;
        $this->httpClient = $httpClient ?: new Http\Curl();
    }

    // -------------------------------------------------------------------------

    /**
     * Perform a request and return a OAI SimpleXML Document
     *
     * @param string $verb
     * Which OAI-PMH verb to use
     *
     * @param array $params
     * An array of key/value parameters
     *
     * @return SimpleXMLElement
     * An XML document
     */
    public function request($verb, $params = array())
    {
        //Build the URL
        $params = array_merge(array('verb' => $verb), $params);
        $url = $this->url . '?' . http_build_query($params);
        
        //Do the request
        $resp = $this->httpClient->request($url);

        //Decode the response
        return $this->decodeResponse($resp);
    }

    // -------------------------------------------------------------------------

    /**
     * Decode the response into XML
     *
     * @param $resp
     * The response body from a HTTP request
     *
     * return SimpleXMLElement
     * An XML document
     */
    private function decodeResponse($resp)
    {
        //Setup a SimpleXML Document
        try {
            $xml = @new \SimpleXMLElement($resp);
        } catch (\Exception $e) {
            throw new Http\RequestException(sprintf("Could not decode XML Response: %s", $e->getMessage()));
        }

        //If we get back a OAI-PMH error, throw a OaipmhRequestException
        if (isset($xml->error)) {
            $code = (string) $xml->error['code'];
            $msg  = (string) $xml->error;

            throw new OaipmhRequestException($code, $msg);
        }

        return $xml;
    }
}

/* EOF: OaipmhClient.php */