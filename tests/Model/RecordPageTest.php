<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 9/20/16
 * Time: 9:06 PM
 */

namespace Phpoaipmh\Model;

use Phpoaipmh\DateGranularity;

/**
 * Class RecordPageTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class RecordPageTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorCreatesNewObject()
    {
        $obj = $this->constructNewInstance();
        $this->assertInstanceOf('\Phpoaipmh\Model\RecordPage', $obj);
    }

    public function testGetters()
    {
        $obj = $this->constructNewInstance();
        $this->assertInstanceOf('\Phpoaipmh\Model\PaginationInfo', $obj->getPaginationInfo());
        $this->assertInstanceOf('\Phpoaipmh\Model\RequestParameters', $obj->getRequestParameters());
        $this->assertInstanceOf('\SimpleXMLElement', current($obj->getRecords()));
    }

    public function testCountRecordsForArray()
    {
        $obj = $this->constructNewInstance();
        $this->assertEquals(3, $obj->countPageRecords());
    }

    public function testCountRecordsForIterator()
    {
        $obj = $this->constructNewInstance(true, true);
        $this->assertEquals(3, $obj->countPageRecords());
    }

    public function testCountRecordsForEmptyPage()
    {
        $obj = $this->constructNewInstance(false);
        $this->assertEquals(0, $obj->countPageRecords());
    }

    public function testBuildFromRawXmlCreatesExpectedValuesForValidXml()
    {
        $xmlData = new \SimpleXMLElement(
            file_get_contents(TEST_DIR . '/fixtures/SampleXML/GoodResponseFourPage_1.xml')
        );

        $obj = RecordPage::buildFromRawXml(
            $xmlData,
            $this->getRequestParameters(),
            new DateGranularity(DateGranularity::DATE_AND_TIME)
        );

        $this->assertInstanceOf('\Phpoaipmh\Model\RecordPage', $obj);
        $this->assertEquals(200, $obj->countPageRecords());
        $this->assertEquals(733, $obj->getPaginationInfo()->getCompleteRecordCount());

        $this->assertEquals(
            '0/200/733/nsdl_dc/null/2012-07-26/null',
            $obj->getPaginationInfo()->getResumptionToken()
        );

        $this->assertInstanceOf('\SimpleXMLElement', current($obj->getRecords()));
    }

    /**
     * @expectedException \Phpoaipmh\Exception\MalformedResponseException
     */
    public function testBuildFromRawXmlThrowsExceptionForInvalidXml()
    {
        $xmlData = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8" ?><valid><but><not><expected/></not></but></valid>'
        );

        $obj = RecordPage::buildFromRawXml(
            $xmlData,
            $this->getRequestParameters(),
            new DateGranularity(DateGranularity::DATE_AND_TIME)
        );
    }

    /**
     * @param bool $hasRecords
     * @param bool $recordIterator
     * @return RecordPage
     */
    protected function constructNewInstance($hasRecords = true, $recordIterator = false)
    {
        $records = ($hasRecords) ? [
            new \SimpleXMLElement('<foo/>'),
            new \SimpleXMLElement('<bar/>'),
            new \SimpleXMLElement('<baz/>')
        ] : [];

        $paginationInfo = new PaginationInfo();

        return new RecordPage(
            $recordIterator ? new \ArrayIterator($records) : $records,
            $paginationInfo,
            $this->getRequestParameters()
        );
    }

    /**
     * Get request parameters
     *
     * @param string $verb
     * @return RequestParameters
     */
    protected function getRequestParameters($verb = 'ListIdentifiers')
    {
        return new RequestParameters('http://example.org', $verb);
    }
}
