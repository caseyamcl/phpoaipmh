<?php

namespace Phpoaipmh\HttpAdapter;

use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Exception\RequestException as GuzzleException;

/**
 * GuzzleAdapter HttpAdapter HttpAdapterInterface Adapter
 *
 * @package Phpoaipmh\HttpAdapter
 */
class GuzzleAdapter extends GuzzleClient implements HttpAdapterInterface
{
    /**
     * Do the request with GuzzleAdapter
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
            throw new RequestExceptionBase($e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }
}

/* EOF: GuzzleAdapter.php */