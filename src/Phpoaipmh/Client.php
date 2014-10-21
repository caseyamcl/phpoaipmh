<?php

namespace Phpoaipmh;

use Phpoaipmh\Http\Client as HttpClient;
use Phpoaipmh\Http\Guzzle;
use Phpoaipmh\Http\Curl;
use RuntimeException;

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
     * @param string $url  The URL of the OAI-PMH Endpoint
     * @param Http\Client $httpClient  Optional HTTP Client class; attempt to auto-build dependency if not passed
     */
    public function __construct($url = null, HttpClient $httpClient = null)
    {
        $this->setUrl($url);

        if ($httpClient) {
            $this->httpClient = $httpClient;
        }
        else {
            $this->httpClient = (class_exists('Guzzle\Http\Client')) ? new Guzzle() : new Curl();
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Set the URL
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    // -------------------------------------------------------------------------

    /**
     * Perform a request and return a OAI SimpleXML Document
     *
     * @param string $verb  Which OAI-PMH verb to use
     * @param array $params  An array of key/value parameters
     * @return \SimpleXMLElement  An XML document
     */
    public function request($verb, array $params = array())
    {
        if ( ! $this->url) {
            throw new RuntimeException("Cannot perform request when URL not set.  Use setUrl() method");
        }

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
     * @param string $resp  The response body from a HTTP request
     * @return \SimpleXMLElement  An XML document
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

/* EOF: Client.php */