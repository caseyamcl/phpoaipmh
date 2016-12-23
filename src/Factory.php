<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 9/20/16
 * Time: 3:35 PM
 */

namespace Phpoaipmh;

use Phpoaipmh\Endpoint\Endpoint;
use Phpoaipmh\HttpAdapter\CurlAdapter;
use Phpoaipmh\HttpAdapter\Guzzle5Adapter;
use Phpoaipmh\HttpAdapter\GuzzleAdapter;
use Phpoaipmh\HttpAdapter\HttpAdapterInterface;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;

/**
 * PHP OAI-PMH Client Factory
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Factory
{
    const AUTO = null;

    /**
     * Build a client
     *
     * @param HttpAdapterInterface $adapter
     */
    public static function client(HttpAdapterInterface $adapter = self::AUTO)
    {
        $that = new static();
        return $that->buildClient($adapter);
    }

    /**
     * Build an endpoint
     *
     * @param string $url
     * @param ClientInterface $client
     */
    public static function endpoint($url, ClientInterface $client = self::AUTO)
    {
        $that = new static();
        $that->buildEndpoint($url, $client);
    }

    /**
     * @return HttpAdapterInterface
     */
    public function detectHttpAdapter()
    {
        if (class_exists('\GuzzleHttp\Client')) {

            $guzzleVersion = (int) substr(GuzzleClientInterface::VERSION, 0, 1);

            if ($guzzleVersion >= 6) {
                return new GuzzleAdapter();
            }
            elseif ($guzzleVersion == 5) {
                return new Guzzle5Adapter();
            }
            else {
                throw new \RuntimeException('Invalid Guzzle version (v5+ required): ' . $guzzleVersion);
            }
        }
        elseif (is_callable('curl_exec')) {
            return new CurlAdapter();
        }
        else {
            throw new \RuntimeException('No cURL extension or Guzzle libraries detected.  Install either, or implement your own HttpAdapterInterface');
        }
    }

    /**
     * Build a client
     *
     * @param HttpAdapterInterface $httpAdapter
     * @return Client
     */
    public function buildClient(HttpAdapterInterface $httpAdapter = self::AUTO)
    {
        return new Client($httpAdapter ?: $this->detectHttpAdapter());
    }

    /**
     * Build an endpoint
     *
     * @param string          $url
     * @param ClientInterface $client
     * @return Endpoint
     */
    public function buildEndpoint($url, ClientInterface $client = self::AUTO)
    {
        return new Endpoint($url, $client ?: $this->buildClient());
    }
}
