<?php

namespace Phpoaipmh\Model;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RecordPageTest extends TestCase
{
    public function testCountReturnsZeroForEmptyPage()
    {
        $pageObj = new RecordPage(new DateTimeImmutable('2018-01-01'), 'ListRecords', []);
        $this->assertSame(0, $pageObj->count());
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
}
