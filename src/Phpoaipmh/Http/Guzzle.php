<?php

namespace Phpoaipmh\Http;

use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Exception\RequestException as GuzzleException;

/**
 * Guzzle Http Client Adapter
 *
 * @package Phpoaipmh\Http
 */
class Guzzle extends GuzzleClient implements Client
{
    /**
     * Do the request with Guzzle
     *
     * @param string $url
     * @return string
     */
    public function request($url)
    {
        try {
            parent::setBaseUrl($url);
            $result = parent::get()->send()->getBody(true);
        }
        catch (GuzzleException $e) {
            throw new RequestException($e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }
}

/* EOF: Guzzle.php */