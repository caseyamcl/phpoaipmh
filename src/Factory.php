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
use Phpoaipmh\HttpAdapter\GuzzleAdapter;
use Phpoaipmh\HttpAdapter\HttpAdapterInterface;

/**
 * PHP OAI-PMH Client Factory
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Factory
{
    const AUTO = null;

    /**
     * @return HttpAdapterInterface
     */
    public function detectHttpAdapter()
    {
        return (class_exists('\GuzzleHttp\Client'))
            ? new CurlAdapter()
            : new GuzzleAdapter();
    }

    /**
     * @param HttpAdapterInterface $httpAdapter
     * @return Client
     */
    public function buildClient(HttpAdapterInterface $httpAdapter = self::AUTO)
    {
        return new Client($httpAdapter ?: $this->detectHttpAdapter());
    }

    /**
     * @param string          $url
     * @param ClientInterface $client
     * @return Endpoint
     */
    public function buildEndpoint($url, ClientInterface $client = self::AUTO)
    {
        return new Endpoint($url, $client ?: $this->buildClient());
    }
}
