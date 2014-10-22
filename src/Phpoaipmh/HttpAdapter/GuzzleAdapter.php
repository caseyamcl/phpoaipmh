<?php

namespace Phpoaipmh\HttpAdapter;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\TransferException as GuzzleException;
use Phpoaipmh\Exception\HttpException;

/**
 * GuzzleAdapter HttpAdapter HttpAdapterInterface Adapter
 *
 * @package Phpoaipmh\HttpAdapter
 */
class GuzzleAdapter implements HttpAdapterInterface
{
    /**
     * @var GuzzleClient
     */
    private $guzzle;

    // ----------------------------------------------------------------

    /**
     * Constructor
     *
     * @param GuzzleClient $guzzle
     */
    public function __construct(GuzzleClient $guzzle = null)
    {
        $this->guzzle = $guzzle ?: new GuzzleClient();
    }

    // ----------------------------------------------------------------

    /**
     * Get the Guzzle Client
     *
     * @return GuzzleClient
     */
    public function getGuzzleClient()
    {
        return $this->guzzle;
    }

    // ----------------------------------------------------------------

    /**
     * Do the request with GuzzleAdapter
     *
     * @param string $url
     * @return string
     * @throws HttpException
     */
    public function request($url)
    {
        try {
            $resp = $this->guzzle->get($url);
            return (string) $resp->getBody();
        }
        catch (GuzzleException $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

/* EOF: GuzzleAdapter.php */