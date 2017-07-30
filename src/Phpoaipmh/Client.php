<?php

/**
 * PHPOAIPMH Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/phpoaipmh
 * @version 2.0
 * @package caseyamcl/phpoaipmh
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace Phpoaipmh;

use Phpoaipmh\Exception\HttpException;
use Phpoaipmh\Exception\OaipmhException;
use Phpoaipmh\Exception\MalformedResponseException;
use Phpoaipmh\HttpAdapter\CurlAdapter;
use Phpoaipmh\HttpAdapter\GuzzleAdapter;
use Phpoaipmh\HttpAdapter\HttpAdapterInterface;
use RuntimeException;

/**
 * OAI-PMH Client class retrieves and decodes OAI-PMH from a given URL
 *
 * @since v1.0
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Client implements ClientInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var HttpAdapterInterface
     */
    private $httpClient;

    /**
     * Constructor
     *
     * @param string               $url        The URL of the OAI-PMH Endpoint
     * @param HttpAdapterInterface $httpClient Optional HTTP HttpAdapterInterface class; attempt to auto-build dependency if not passed
     */
    public function __construct($url = null, HttpAdapterInterface $httpClient = null)
    {
        $this->setUrl($url);

        if ($httpClient) {
            $this->httpClient = $httpClient;
        } else {
            $this->httpClient = (class_exists('GuzzleHttp\Client'))
                ? new GuzzleAdapter()
                : new CurlAdapter();
        }
    }

    /**
     * Set the URL
     *
     * @param string $url
     * @deprecated Will be removed in v3.0.  Build a new client instance instead
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Perform a request and return a OAI SimpleXML Document
     *
     * @param  string            $verb   Which OAI-PMH verb to use
     * @param  array             $params An array of key/value parameters
     * @return \SimpleXMLElement An XML document
     */
    public function request($verb, array $params = array())
    {
        if (! $this->url) {
            throw new RuntimeException("Cannot perform request when URL not set.  Use setUrl() method");
        }

        //Build the URL
        $params = array_merge(array('verb' => $verb), $params);
        $url = $this->url . (parse_url($this->url, PHP_URL_QUERY) ? '&' : '?') . http_build_query($params);

        //Do the request
        try {
            $resp = $this->httpClient->request($url);
        } catch (HttpException $e) {
            $this->checkForOaipmhException($e);
            $resp = '';
        }

        return $this->decodeResponse($resp);
    }

    /**
     * Check for OAI-PMH Exception from HTTP Exception
     *
     * Converts a HttpException into an OAI-PMH exception if there is an
     * OAI-PMH Error Code.
     *
     * @param HttpException $httpException
     */
    private function checkForOaipmhException(HttpException $httpException)
    {
        try {
            if ($resp = $httpException->getBody()) {
                $this->decodeResponse($resp); // Throw OaipmhException in case of an error
            }
        } catch (MalformedResponseException $e) {
            // There was no valid OAI error in the response, therefore re-throw HttpException
        }

        throw $httpException;
    }

    /**
     * Decode the response into XML
     *
     * @param  string            $resp The response body from a HTTP request
     * @return \SimpleXMLElement An XML document
     */
    protected function decodeResponse($resp)
    {
        //Setup a SimpleXML Document
        try {
            $xml = @new \SimpleXMLElement($resp);
        } catch (\Exception $e) {
            throw new MalformedResponseException(sprintf("Could not decode XML Response: %s", $e->getMessage()));
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
