<?php

namespace Phpoaipmh;

use Phpoaipmh\Exception\OaipmhException;
use Phpoaipmh\Exception\ResponseMalformedException;
use Phpoaipmh\HttpAdapter\CurlAdapter;
use Phpoaipmh\HttpAdapter\GuzzleAdapter;
use Phpoaipmh\HttpAdapter\HttpAdapterInterface;
use RuntimeException;

/**
 * OAI-PMH Client class retrieves and decodes OAI-PMH from a given URL
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Client
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var HttpAdapterInterface
     */
    private $httpClient;

    // -------------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $url  The URL of the OAI-PMH Endpoint
     * @param HttpAdapterInterface $httpClient  Optional HTTP HttpAdapterInterface class; attempt to auto-build dependency if not passed
     */
    public function __construct($url = null, HttpAdapterInterface $httpClient = null)
    {
        $this->setUrl($url);

        if ($httpClient) {
            $this->httpClient = $httpClient;
        }
        else {
            $this->httpClient = (class_exists('Guzzle\Http\Client')) ? new GuzzleAdapter() : new CurlAdapter();
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
    protected function decodeResponse($resp)
    {
        //Setup a SimpleXML Document
        try {
            $xml = @new \SimpleXMLElement($resp);
        } catch (\Exception $e) {
            throw new ResponseMalformedException(sprintf("Could not decode XML Response: %s", $e->getMessage()));
        }

        //If we get back a OAI-PMH error, throw a OaipmhException
        if (isset($xml->error)) {
            $code = (string) $xml->error['code'];
            $msg  = (string) $xml->error;

            throw new OaipmhException($code, $msg);
        }

        return $xml;
    }
}

/* EOF: HttpAdapterInterface.php */