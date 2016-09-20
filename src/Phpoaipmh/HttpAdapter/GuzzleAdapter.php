<?php

namespace Phpoaipmh\HttpAdapter;

use GuzzleHttp\Client as GuzzleClient;

/**
 * GuzzleAdapter HttpAdapter HttpAdapterInterface Adapter
 *
 * @package Phpoaipmh\HttpAdapter
 */
class GuzzleAdapter implements HttpAdapterInterface
{
    /**
     * @var array  Default Guzzle Options; these are ignored if GuzzleClient passed to constructor
     */
    private static $defaultGuzzleOptions = [
        'connect_timeout'    => 10,
        'timeout'            => 60,
        'allow_redirects'    => 3,
        'headers'            => ['User-Agent' => 'PHP OAI-PMH Library'],
    ];

    /**
     * @var GuzzleClient
     */
    private $guzzle;

    /**
     * Constructor
     *
     * @param GuzzleClient $guzzle
     */
    public function __construct(GuzzleClient $guzzle = null)
    {
        $this->guzzle = $guzzle ?: new GuzzleClient(static::$defaultGuzzleOptions);
    }

    /**
     * Get the Guzzle Client
     *
     * @return GuzzleClient
     */
    public function getGuzzleClient()
    {
        return $this->guzzle;
    }

    /**
     * Do the request with GuzzleAdapter
     *
     * @param  string  $url
     * @param  array   $queryParams
     * @return string
     */
    public function request($url, array $queryParams = [])
    {
        $response = $this->guzzle->get($url, ['query' => $queryParams]);
        return (string) $response->getBody();
    }
}
