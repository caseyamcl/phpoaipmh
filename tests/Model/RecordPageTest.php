<?php

namespace Phpoaipmh\Model;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RecordPageTest extends TestCase
{
    private const GOOD_XML_FILEPATH = __DIR__ . '/../Fixture/SampleXML/GoodResponseFourPage_1.xml';

    public function testCountReturnsZeroForEmptyPage(): void
    {
        $pageObj = new RecordPage(new DateTimeImmutable('2018-01-01'), 'ListRecords', []);
        $this->assertSame(0, $pageObj->count());
    }

    public function testFromXmlStringReturnsValidObjectWhenPassedValidXml(): void
    {
        $pageObj = RecordPage::fromXmlString(file_get_contents(self::GOOD_XML_FILEPATH));
        $this->assertInstanceOf(RecordPage::class, $pageObj);
        $this->assertSame(200, $pageObj->count());
    }

    public function testGetRecords()
    {

    }

    public function testGetResumptionToken()
    {

    }

    public function testGetVerb()
    {

    }

    public function testGetParams()
    {

    }

    public function testGetIterator()
    {

    }

    public function testGetResponseDate()
    {

    }

    /**
     * @return RecordPage
     */
    private function buildObjectFromXmlFile(): RecordPage
    {
//        return
    }
}
