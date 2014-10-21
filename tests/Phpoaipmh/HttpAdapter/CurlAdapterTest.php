<?php

namespace Phpoaipmh\Http;
use Phpoaipmh\HttpAdapter\CurlAdapter;

/**
 * Class CurlAdapterTest
 *
 * @package Phpoaipmh\Http
 */
class CurlAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Simple Instantiation Test
     *
     * Tests that no syntax or runtime errors occur during object insantiation,
     * and that the class implements the HttpAdapter\HttpAdapterInterface interface
     */
    public function testInsantiateCreatesNewObject()
    {    
        $obj = new CurlAdapter();
        $this->assertInstanceOf('Phpoaipmh\HttpAdapter\CurlAdapter', $obj);
    }

    // -------------------------------------------------------------------------

    /**
     * Simple URL Call Test - Will fail with no internet connectivity
     *
     * Also tests 301 redirects, since http://example.org will redirect
     */
    public function testGoodRequestReturnsContentBody()
    {
        $obj = new CurlAdapter();
        $res = $obj->request('http://example.org');
        $this->assertTrue(strpos($res, "<body>") != false, "The response should include a <body> tag, since it is a HTML document");
    }

    // -------------------------------------------------------------------------

    /**
     * Tests that a non-existent resource (HTTP 404) throws an exception
     */
    public function test404ResponseThrowsAnException()
    {
        $this->setExpectedException('Phpoaipmh\Exception\HttpException');

        $obj = new CurlAdapter();
        $obj->request('http://w3.org/doesnotexistyo');
    }

    // -------------------------------------------------------------------------

    /**
     * Tests that a non-existent server throws an exception
     */
    public function testNonExistentServerThrowsException()
    {
        $this->setExpectedException('Phpoaipmh\Exception\HttpException');

        $obj = new CurlAdapter();
        $obj->request('http://doesnotexist.blargasdf');
    }
}

/* EOF: PhpOaipmhClientTest.php */