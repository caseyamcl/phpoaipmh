<?php

require_once('../src/Phpoaipmh/Http/Client.php');
require_once('../src/Phpoaipmh/Http/Curl.php');
require_once('../src/Phpoaipmh/Http/RequestException.php');

class HttpCurlTest extends PHPUnit_Framework_TestCase {

    // -------------------------------------------------------------------------

    public function setUp()
    {
        parent::setUp();
    }

    // -------------------------------------------------------------------------

    public function tearDown()
    {
        parent::tearDown();
    }

    // -------------------------------------------------------------------------

    /**
     * Simple Instantiation Test
     *
     * Tests that no syntax or runtime errors occur during object insantiation,
     * and that the class implements the Http\Client interface
     */
    public function testInsantiateCreatesNewObject()
    {    
        $obj = new Phpoaipmh\Http\Curl();
        $this->assertInstanceOf('Phpoaipmh\Http\Curl', $obj);
        $this->assertInstanceOf('Phpoaipmh\Http\Client', $obj);

    }

    // -------------------------------------------------------------------------

    /**
     * Simple URL Call Test - Will fail with no internet connectivity
     *
     * Also tests 301 redirects, since http://example.org will redirect
     */
    public function testGoodRequestReturnsContentBody()
    {
        $obj = new Phpoaipmh\Http\Curl();
        $res = $obj->request('http://example.org');
        $this->assertTrue(strpos($res, "<body>") != false, "The response should include a <body> tag, since it is a HTML document");
    }

    // -------------------------------------------------------------------------

    /**
     * Tests that a non-existent resource (HTTP 404) throws an exception
     */
    public function test404ResponseThrowsAnException()
    {
        $this->setExpectedException('Phpoaipmh\Http\RequestException');

        $obj = new Phpoaipmh\Http\Curl();
        $res = $obj->request('http://w3.org/doesnotexistyo');
    }

    // -------------------------------------------------------------------------

    /**
     * Tests that a non-existent server throws an exception
     */
    public function testNonExistentServerThrowsException()
    {
        $this->setExpectedException('Phpoaipmh\Http\RequestException');
        $obj = new Phpoaipmh\Http\Curl();
        $res = $obj->request('http://doesnotexist.blargasdf');
    }
}

/* EOF: PhpOaipmhClientTest.php */