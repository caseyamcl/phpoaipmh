<?php

namespace Phpoaipmh;

use Phpoaipmh\HttpAdapter\HttpAdapterInterface;
use Phpoaipmh\Model\RequestParameters;
use PHPUnit_Framework_TestCase;

/**
 * Created by PhpStorm.
 * User: casey
 * Date: 9/20/16
 * Time: 9:52 PM
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiateCreatesObject()
    {
        $obj = new Client($this->getMockHttpClient('GoodResponseIdentify.xml'));
        $this->assertInstanceOf('\Phpoaipmh\Client', $obj);
    }

    public function testGetDateGranularityReturnsExpectedValue()
    {
        $obj = new Client($this->getMockHttpClient('GoodResponseIdentify.xml'));
        $granularity = $obj->getDateGranularity('http://example.org');

        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-01-01 12:15:10');
        $this->assertEquals('2016-01-01T12:15:10Z', $granularity->formatDate($dateTime));
    }

    public function testGetRecordReturnsExpectedValue()
    {
        $obj = new Client($this->getMockHttpClient('GoodResponseSingleRecord.xml'));
        $record = $obj->getRecord(new RequestParameters('http://example.org', 'GetRecord'));

        $this->assertEquals(
            'oai:nsdl.org:2200/20061003062336249T',
            (string) $record->GetRecord->record->header->identifier
        );
    }

    /**
     * @param string $sampleXMLfile Filename in tests/fixtures/SampleXML
     * @return \Mockery\MockInterface|HttpAdapterInterface
     */
    protected function getMockHttpClient($sampleXMLfile)
    {
        $filePath = TEST_DIR . '/fixtures/SampleXML/' . $sampleXMLfile;

        $mock = \Mockery::mock('Phpoaipmh\HttpAdapter\HttpAdapterInterface');
        $mock->shouldReceive('request')->andReturn(file_get_contents($filePath));

        return $mock;

    }
}
