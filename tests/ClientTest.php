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

    public function testIteratePagesReturnsPageValidPageObjects()
    {
        $files = [
            'GoodResponseFourPage_1.xml',
            'GoodResponseFourPage_2.xml',
            'GoodResponseFourPage_3.xml',
            'GoodResponseFourPage_4.xml'
        ];

        $obj = new Client($this->getMockHttpClient($files));

        $firstRecordOnEachPage = [];
        foreach ($obj->iteratePages(new RequestParameters('http://example.org', 'ListIdentifiers')) as $page) {
            $this->assertInstanceOf('Phpoaipmh\Model\RecordPage', $page);
            $firstRecordOnEachPage[] = current($page->getRecords());
        }

        $this->assertEquals(4, count($firstRecordOnEachPage));

        foreach ($firstRecordOnEachPage as $record) {
            var_dump($record);
        }
    }

    public function testGetNumTotalRecordsReturnsExpectedValue()
    {
        $files = ['GoodResponseFourPage_1.xml'];

        $obj = new Client($this->getMockHttpClient($files));
        $params = new RequestParameters('http://example.org', 'ListIdentifiers');

        $numTotalRecs = $obj->getNumTotalRecords($params);
        $this->assertEquals(733, $numTotalRecs);
    }

    public function testIterateRecordsReturnsExpectedValue()
    {
        $files = [
            'GoodResponseFourPage_1.xml',
            'GoodResponseFourPage_2.xml',
            'GoodResponseFourPage_3.xml',
            'GoodResponseFourPage_4.xml'
        ];

        $obj = new Client($this->getMockHttpClient($files));

        $recordCount = 0;
        foreach ($obj->iterateRecords(new RequestParameters('http://example.org', 'ListIdentifiers')) as $page) {
            $recordCount++;
        }
        $this->assertEquals(733, $recordCount);
    }

    /**
     * @param string|array|string[] $sampleXMLfiles Filename in tests/fixtures/SampleXML
     * @return \Mockery\MockInterface|HttpAdapterInterface
     */
    protected function getMockHttpClient($sampleXMLfiles)
    {
        $output = [];
        foreach ((array) $sampleXMLfiles as $sampleXMLfile) {
            $filePath = TEST_DIR . '/fixtures/SampleXML/' . $sampleXMLfile;
            $output[] = file_get_contents($filePath);
        }

        $mock = \Mockery::mock('Phpoaipmh\HttpAdapter\HttpAdapterInterface');
        $mock->shouldReceive('request')->andReturnValues($output);

        return $mock;

    }
}
