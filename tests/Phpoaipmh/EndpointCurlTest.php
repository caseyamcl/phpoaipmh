<?php

namespace Phpoaipmh;
use PHPUnit_Framework_TestCase;
use Guzzle\Http\Client as GuzzleHttpClient;

class EndpointCurlTest extends PHPUnit_Framework_TestCase
{
    // --------------------------------------------------------------

    const ENDPOINT_URL    = 'http://nsdl.org/oai';
    const METADATA_PREFIX = 'oai_dc';
    const RECORD_ID       = 'oai:nsdl.org:2200/20121012134007915T';

    // --------------------------------------------------------------

    protected function setUp()
    {
        //Send a request to the endpoint
        $guzzle = new GuzzleHttpClient(self::ENDPOINT_URL);
        $response = $guzzle->get('?verb=Identify')->send();

        //Search for a known string to ensure the endpoint works before testing it
        $searchFor = "<Identify>";
        if (strpos((string) $response->getBody(), $searchFor) === false) {
            $this->markTestSkipped(sprintf('Could not connect to test repository at: %s', self::ENDPOINT_URL));
        }
    }

    // --------------------------------------------------------------

    public function testGetRecordWorksForCorrectRecord()
    {
        //First, make sure the record actually exists
        $guzzle   = new GuzzleHttpClient(self::ENDPOINT_URL);
        $uri      = sprintf("?verb=GetRecord&identifier=%s&metadataPrefix=%s", self::RECORD_ID, self::METADATA_PREFIX);
        $response = $guzzle->get($uri)->send();

        //Search for a known string to ensure the endpoint works before testing it
        $searchFor = "<GetRecord>";
        if (strpos((string) $response->getBody(), $searchFor) === false) {
            $this->markTestSkipped(sprintf('Could not find test record: %s', self::RECORD_ID));
        }

        //Then, run the test using the Endpoint Library
        $endpoint = $this->getObj();
        $result = $endpoint->getRecord(self::RECORD_ID, self::METADATA_PREFIX);

        $this->assertInstanceOf('\SimpleXMLElement', $result);
        $this->assertEquals((string) $result->GetRecord->record->header->identifier[0], self::RECORD_ID);
    }

    // --------------------------------------------------------------

    /**
     * @depends testGetRecordWorksForCorrectRecord
     * @expectedException \Phpoaipmh\OaipmhRequestException
     */
    public function testGetRecordThrowsOAIExceptionForInvalidRecord()
    {
        $endpoint = $this->getObj();
         $endpoint->getRecord('thisISTotalFake'. rand(1000, 4000), self::METADATA_PREFIX);
    }

    // --------------------------------------------------------------

    protected function getObj()
    {
        return new Endpoint(new Client(self::ENDPOINT_URL, $this->getHttpClientObj()));
    }

    // --------------------------------------------------------------

    protected function getHttpClientObj()
    {
        return new Http\Curl();
    }
}

/* EOF: EndpointRealTest.php */