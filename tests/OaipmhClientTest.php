<?php

require_once('../src/Phpoaipmh/Client.php');
require_once('../src/Phpoaipmh/HttpException.php');
require_once('../src/Phpoaipmh/OaipmhReqeustException.php');

class OaipmhClientTest extends PHPUnit_Framework_TestCase {

    private $testurl = 'http://nsdl.org/oai';

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testInsantiateCreatesNewObject() {

        $obj = new Phpoaipmh\Client($this->testurl);
        $this->assertInstanceOf('Phpoaipmh\Client', $obj);

    }

    public function testRequestRuns() {
        $obj = new Phpoaipmh\Client($this->testurl);
        var_dump($obj->request('Identify'));
    }
}

/* EOF: PhpOaipmhClientTest.php */