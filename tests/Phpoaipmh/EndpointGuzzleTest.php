<?php

namespace Phpoaipmh;
use PHPUnit_Framework_TestCase;

require_once __DIR__ . '/EndpointCurlTest.php';

class EndpointGuzzleTest extends EndpointCurlTest
{
    protected function getHttpClientObj()
    {
        return new Http\Guzzle();
    }    
}

/* EOF: EndpointGuzzleTest.php */