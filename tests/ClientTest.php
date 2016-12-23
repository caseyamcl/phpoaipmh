<?php

namespace Phpoaipmh;

use Phpoaipmh\HttpAdapter\HttpAdapterInterface;
use Phpoaipmh\Model\RecordPage;
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

    public function testGetDateGranularityReturnsExpectedValueWhenSetToAuto()
    {
        $obj = new Client($this->getMockHttpClient('GoodResponseIdentify.xml'));
        $granularity = $obj->getDateGranularity('http://example.org');

        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-01-01 12:15:10');
        $this->assertEquals('2016-01-01T12:15:10Z', $granularity->formatDate($dateTime));
    }

    public function testGetDateGranularityReturnsExpectedValueWhenExplictelyProvided()
    {
        $granularity = DateGranularity::date();
        $obj = new Client($this->getMockHttpClient([]), $granularity);

        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-01-01 12:15:10');
        $this->assertEquals('2016-01-01', $obj->getDateGranularity('http://example.org')->formatDate($dateTime));
    }

    public function testGetRecordReturnsExpectedValue()
    {
        $obj = $this->getClientWithDateGranularitySet(
            $this->getMockHttpClient('GoodResponseSingleRecord.xml')
        );

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

        $obj = $this->getClientWithDateGranularitySet($this->getMockHttpClient($files));

        $firstRecordOnEachPage = [];
        foreach ($obj->iteratePages(new RequestParameters('http://example.org', 'ListIdentifiers')) as $page) {
            $this->assertInstanceOf(RecordPage::class, $page);
            $firstRecordOnEachPage[] = current($page->getRecords());
        }

        $this->assertEquals(4, count($firstRecordOnEachPage));
    }

    public function testGetNumTotalRecordsReturnsExpectedValue()
    {
        $files = ['GoodResponseFourPage_1.xml'];

        $obj = $this->getClientWithDateGranularitySet($this->getMockHttpClient($files));
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

        $obj = $this->getClientWithDateGranularitySet($this->getMockHttpClient($files));

        $recordCount = 0;
        foreach ($obj->iterateRecords(new RequestParameters('http://example.org', 'ListIdentifiers')) as $page) {
            $recordCount++;
        }
        $this->assertEquals(733, $recordCount);
    }

    /**
     * Get a client object with the date granularity set
     *
     * This keeps the client from generating an extra request to
     * get the date granularity during tests, which can cause confusion.
     *
     * @param HttpAdapterInterface $httpClient
     * @return Client
     */
    protected function getClientWithDateGranularitySet(HttpAdapterInterface $httpClient)
    {
        return new Client($httpClient, new DateGranularity(DateGranularity::DATE_AND_TIME));
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

        $mock = \Mockery::mock(HttpAdapterInterface::class);
        $mock->shouldReceive('request')->andReturnUsing(function() use (&$output) {
            if (! empty($output)) {
                return array_shift($output);
            }
            else {
                throw new \Exception("No response from Mock HTTP Client");
            }
        });

        return $mock;

    }
}
