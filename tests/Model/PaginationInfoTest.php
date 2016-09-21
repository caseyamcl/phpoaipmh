<?php

namespace Phpoaipmh\Model;

use PHPUnit_Framework_TestCase;

/**
 * Created by PhpStorm.
 * User: casey
 * Date: 9/20/16
 * Time: 8:38 PM
 */
class PaginationInfoTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiateCreatesNewObject()
    {
        $obj = new PaginationInfo();
        $this->assertInstanceOf('Phpoaipmh\Model\PaginationInfo', $obj);
    }

    public function testResumptionToken()
    {
        $hasNoToken = new PaginationInfo();
        $this->assertFalse($hasNoToken->hasResumptionToken());
        $this->assertEmpty($hasNoToken->getResumptionToken());

        $hasToken = new PaginationInfo('resumption-token');
        $this->assertTrue($hasToken->hasResumptionToken());
        $this->assertEquals('resumption-token', $hasToken->getResumptionToken());
    }

    public function testCompleteRecordCount()
    {
        $hasNoCount = new PaginationInfo();
        $this->assertFalse($hasNoCount->hasCompleteRecordCount());
        $this->assertEmpty($hasNoCount->getCompleteRecordCount());

        $hasCount = new PaginationInfo('', 100);
        $this->assertTrue($hasCount->hasCompleteRecordCount());
        $this->assertEquals(100, $hasCount->getCompleteRecordCount());
    }

    public function testExpirationDate()
    {
        $hasNoExpDate = new PaginationInfo();
        $this->assertFalse($hasNoExpDate->hasExpirationDate());
        $this->assertNull($hasNoExpDate->getExpirationDate());

        $dt = new \DateTime();
        $hasExpDate = new PaginationInfo('', null, $dt);
        $this->assertTrue($hasExpDate->hasExpirationDate());
        $this->assertEquals($dt, $hasExpDate->getExpirationDate());
    }
}
