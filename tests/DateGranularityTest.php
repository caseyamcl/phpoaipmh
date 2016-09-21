<?php

namespace Phpoaipmh;

use PHPUnit_Framework_TestCase;

/**
 * Created by PhpStorm.
 * User: casey
 * Date: 9/20/16
 * Time: 9:43 PM
 */
class DateGranularityTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiateReturnsValidObject()
    {
        $obj = new DateGranularity(DateGranularity::DATE);
        $this->assertInstanceOf('Phpoaipmh\DateGranularity', $obj);

        $obj = new DateGranularity(DateGranularity::DATE_AND_TIME);
        $this->assertInstanceOf('Phpoaipmh\DateGranularity', $obj);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInstantiateThrowsExceptionForInvalidFormat()
    {
        $obj = new DateGranularity('not-valid');
    }

    public function testFormatDateReturnsExpectedForDateFormat()
    {
        $obj = new DateGranularity(DateGranularity::DATE);
        $value = $obj->formatDate(\DateTime::createFromFormat('Y-m-d H:i:s', '2016-01-01 12:20:15'));
        $this->assertEquals('2016-01-01', $value);
    }

    public function testFormatDateReturnsExpectedForDateAndTimeFormat()
    {
        $obj = new DateGranularity(DateGranularity::DATE_AND_TIME);
        $value = $obj->formatDate(\DateTime::createFromFormat('Y-m-d H:i:s', '2016-01-01 12:20:15'));
        $this->assertEquals('2016-01-01T12:20:15Z', $value);
    }

    public function testCreateDateTimeObjectReturnsExpectedForDateFormat()
    {
        $obj = new DateGranularity(DateGranularity::DATE);
        $value  = $obj->createDateTimeObject('2016-01-01');
        $this->assertEquals('2016-01-01', $value->format('Y-m-d'));
    }

    public function testCreateDateTimeObjectReturnsExpectedForDateTimeFormat()
    {
        $obj = new DateGranularity(DateGranularity::DATE_AND_TIME);
        $value  = $obj->createDateTimeObject('2016-01-01T12:20:15Z');
        $this->assertEquals('2016-01-01 12:20:15', $value->format('Y-m-d H:i:s'));
    }
}
