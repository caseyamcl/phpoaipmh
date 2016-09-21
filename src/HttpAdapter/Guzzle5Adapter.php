<?php

namespace Phpoaipmh\HttpAdapter;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;

/**
 * GuzzleAdapter HttpAdapter HttpAdapterInterface Adapter
 *
 * @package Phpoaipmh\HttpAdapter
 */
class Guzzle5Adapter implements HttpAdapterInterface
{
    /**
     * @var array  Default Guzzle Options; these are ignored if GuzzleClient passed to constructor
     */
    private static $defaultGuzzleOptions = [
        'connect_timeout'    => 10,
        'timeout'            => 60,
        'headers'            => ['User-Agent' => 'PHP OAI-PMH Library'],
        'allow_redirects'    => [
            'max'       => 3,
            'strict'    => true,
            'referer'   => true,
            'protocols' => ['http', 'https']
        ]
    ];

    /**
     * @var GuzzleClient
     */
    private $guzzle;

    /**
     * Constructor
     *
     * @param GuzzleClient $guzzle
     * @throws \Exception  If using wrong Guzzle version
     */
    public function __construct(GuzzleClient $guzzle = null)
    {
        if (substr(ClientInterface::VERSION, 0, 1) != '5') {
            throw new \Exception(sprintf(
                'Guzzle is at Version %s.  %s requires Guzzle v5 (should you be using %s instead?)',
                ClientInterface::VERSION,
                get_called_class(),
                GuzzleAdapter::class
            ));
        }

        $this->guzzle = $guzzle ?: new GuzzleClient([
            'defaults' => self::$defaultGuzzleOptions
        ]);
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
     * @param string $url
     * @param array  $queryParams
     * @return string
     */
    public function request($url, array $queryParams = [])
    {
        $resp = $this->guzzle->get($url, ['query' => $queryParams]);
        return (string) $resp->getBody();
    }
}
