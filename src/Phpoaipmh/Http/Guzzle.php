<?php

namespace Phpoaipmh\Http;
use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Common\GuzzleException

class Guzzle extends GuzzleClient implements Client
{
    public function request($url)
    {
        try {
            parent::setBaseUrl($url);
            $result = parent::get()->send()->getBody();
        }
        catch (GuzzleException $e) {
            throw new RequestException($e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }
}

/* EOF: Guzzle.php */