<?php

require_once('../src/Phpoaipmh/Client.php');
require_once('../src/Phpoaipmh/HttpException.php');
require_once('../src/Phpoaipmh/OaipmhRequestException.php');

class PhpOaipmhClientTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testInsantiateCreatesNewObject() {

        $obj = new Phpoaipmh\Client('http://example.com');
        $this->assertInstanceOf('Phpoaipmh\Client', $obj);

    }

    public function testRequestRuns() {
        $obj = new Phpoaipmh\Client('http://example.com');
        $obj->request();
    }
}

/* EOF: PhpOaipmhClientTest.php */