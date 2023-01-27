<?php

/**
 * PHPOAIPMH Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/phpoaipmh
 * @version 3.0
 * @package caseyamcl/phpoaipmh
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, -please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace Phpoaipmh;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Endpoint Test
 *
 * @package Phpoaipmh
 */
class EndpointTest extends TestCase
{

    /**
     * Simple Instantiation Test
     *
     * Tests that no syntax or runtime errors occur during object insantiation
     */
    public function testInsantiateCreatesNewObject()
    {
        $obj = new Endpoint($this->getMockClient());
        $this->assertInstanceOf(Endpoint::class, $obj);
        $this->assertInstanceOf(EndpointInterface::class, $obj);
    }

    /**
     * Test that identify returns a valid SimpleXMLElement
     */
    public function testIdentifyReturnsSimpleXMLDocument()
    {
        //Build mock object
        $retVal = simplexml_load_file($this->getSampleXML('GoodResponseIdentify.xml'));
        $client = $this->getMockClient($retVal);

        //Do it
        $obj = new Endpoint($client);
        $response = $obj->identify();

        //Check results
        $this->assertInstanceof('SimpleXMLElement', $response);
        $this->assertObjectHasAttribute('Identify', $response);
    }

    /**
     * Test meta data format being returned
     */
    public function testListMetadataFormatsReturnsRecordIterator()
    {
        $client = $this->getMockClient();
        $obj = new Endpoint($client);

        $returnValue = $obj->listMetadataFormats();

        //Check results
        $expectedRecordIterator = new RecordIterator($client, "ListMetadataFormats");
        $this->assertEquals($expectedRecordIterator, $returnValue);
    }

    /**
     * Test meta data format for record being returned
     */
    public function testListMetadataFormatsForRecordReturnsRecordIterator()
    {
        $client = $this->getMockClient();
        $obj = new Endpoint($client);

        $returnValue = $obj->listMetadataFormats("recordId");

        //Check results
        $expectedRecordIterator = new RecordIterator($client, "ListMetadataFormats", array('identifier' => "recordId"));
        $this->assertEquals($expectedRecordIterator, $returnValue);
    }

    /**
     * Test meta data format being returned
     */
    public function testListSetsReturnsRecordIterator()
    {
        $client = $this->getMockClient();
        $obj = new Endpoint($client);

        $returnValue = $obj->listSets();

        //Check results
        $expectedRecordIterator = new RecordIterator($client, "ListSets");
        $this->assertEquals($expectedRecordIterator, $returnValue);
    }

    /**
     * Test that client is called correctly
     */
    public function testGetRecordCallsClient()
    {
        $client = $this->getMockClient();
        $client
            ->expects($this->once())
            ->method("request")
            ->with("GetRecord", array(
                'identifier' => "recordId",
                'metadataPrefix' => "metadataPrefix",
            ));

        $obj = new Endpoint($client);
        $obj->getRecord("recordId", "metadataPrefix");
    }

    /**
     * Test that record is returned
     */
    public function testGetRecordReturnsRecord()
    {
        $retVal = simplexml_load_file($this->getSampleXML('GoodResponseSingleRecord.xml'));
        $client = $this->getMockClient($retVal);

        $obj = new Endpoint($client);

        $response = $obj->getRecord("recordId", "metadataPrefix");

        //Check results
        $this->assertInstanceof('SimpleXMLElement', $response);
        $this->assertObjectHasAttribute('GetRecord', $response);
    }

    /**
     * Test that ListIdentifiers returns a RecordIterator
     */
    public function testListIdentifiersReturnsRecordIterator()
    {
        $client = $this->getMockClient();
        $obj = new Endpoint($client);

        $returnValue = $obj->listIdentifiers("metadataPrefix");

        $expectedParams = array('metadataPrefix' => "metadataPrefix",);
        $expectedRecordIterator = new RecordIterator($client, "ListIdentifiers", $expectedParams);
        $this->assertEquals($expectedRecordIterator, $returnValue);
    }

    /**
     * Test that ListIdentifiers returns a RecordIterator
     */
    public function testListIdentifiersReturnsRecordIteratorWithParameters()
    {
        $client = $this->getMockClient();
        $obj = new Endpoint($client);

        $returnValue = $obj->listIdentifiers(
            "metadataPrefix",
            new \DateTime("2014-01-01"),
            new \DateTime("2015-01-01"),
            "setSpec",
            "0/200/733/nsdl_dc/null/2012-07-26/null"
        );

        $expectedParams = array(
            'metadataPrefix' => "metadataPrefix",
            'from' => "2014-01-01",
            'until' => "2015-01-01",
            'set' => "setSpec",
        );
        $expectedRecordIterator = new RecordIterator(
            $client,
            "ListIdentifiers",
            $expectedParams,
            "0/200/733/nsdl_dc/null/2012-07-26/null"
        );

        $this->assertEquals($expectedRecordIterator, $returnValue);
    }

    /**
     * Test that list ListRecords returns a RecordIterator
     */
    public function testListRecordsReturnsRecordIterator()
    {
        $client = $this->getMockClient();
        $obj = new Endpoint($client);

        $returnValue = $obj->listRecords("metadataPrefix");

        $expectedParams = array('metadataPrefix' => "metadataPrefix",);
        $expectedRecordIterator = new RecordIterator($client, "ListRecords", $expectedParams);
        $this->assertEquals($expectedRecordIterator, $returnValue);
    }

    /**
     * Test that list ListRecords returns a RecordIterator
     */
    public function testListRecordsReturnsRecordIteratorWithParameters()
    {
        $client = $this->getMockClient();
        $obj = new Endpoint($client, Granularity::DATE);

        $returnValue = $obj->listRecords(
            "metadataPrefix",
            new \DateTime("2014-01-01"),
            new \DateTime("2015-01-01"),
            "setSpec",
            "0/200/733/nsdl_dc/null/2012-07-26/null"
        );

        $expectedParams = array(
            'metadataPrefix' => "metadataPrefix",
            'from' => "2014-01-01",
            'until' => "2015-01-01",
            'set' => "setSpec",
        );

        $expectedRecordIterator = new RecordIterator(
            $client,
            "ListRecords",
            $expectedParams,
            "0/200/733/nsdl_dc/null/2012-07-26/null"
        );

        $this->assertEquals($expectedRecordIterator, $returnValue);
    }

    /**
     * As of v3.0, we don't allow string dates
     */
    public function testStringDateThrowsExceptionOnFromDate()
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = new Endpoint($this->getMockClient());
        $obj->listRecords('oai_dc', '2014-01-01');
    }

    /**
     * As of v3.0, we don't allow string dates
     */
    public function testStringDateThrowsExceptionOnUntilDate()
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = new Endpoint($this->getMockClient());
        $obj->listRecords('oai_dc', null, '2015-01-01');
    }

    /**
     * Shortcut to load contents of a sample XML file
     */
    protected function getSampleXML($file)
    {
        return __DIR__ . '/SampleXML/' . $file;
    }

    protected function getMockClient($retVal = null)
    {
        $mock = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $mock->expects($this->any())->method('request')->will($this->returnValue($retVal));
        return $mock;
    }
}
