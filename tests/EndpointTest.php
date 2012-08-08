<?php

require_once(__DIR__ . '/../src/Phpoaipmh/Endpoint.php');
require_once(__DIR__ . '/../src/Phpoaipmh/Client.php');
require_once(__DIR__ . '/../src/Phpoaipmh/ResponseList.php');

class EndpointTest extends PHPUnit_Framework_TestCase {

    // -------------------------------------------------------------------------

    public function setUp()
    {
        parent::setUp();
    }

    // -------------------------------------------------------------------------

    public function tearDown()
    {
        parent::tearDown();
    }

    // -------------------------------------------------------------------------

    /**
     * Simple Instantiation Test
     *
     * Tests that no syntax or runtime errors occur during object insantiation
     */
    public function testInsantiateCreatesNewObject()
    {    
        $obj = new Phpoaipmh\Endpoint('http://example.com/oai', $this->getMockClient());
        $this->assertInstanceOf('Phpoaipmh\Endpoint', $obj);
    }    

    // -------------------------------------------------------------------------

    /**
     * Test that identify returns a valid SimpleXMLElement
     */
    public function testIdentifyReturnsSimpleXMLDocument()
    {
        //Build mock object
        $retVal = simplexml_load_file($this->getSampleXML('GoodResponseIdentify.xml'));
        $client = $this->getMockClient($retVal);

        //Do it
        $obj = new Phpoaipmh\Endpoint('http://example.com/oai', $client);
        $response = $obj->identify();

        //Check results
        $this->assertInstanceof('SimpleXMLElement', $response);
        $this->assertObjectHasAttribute('Identify', $response);
    }

    // -------------------------------------------------------------------------

    /**
     * LEFT OFF HERE LEFT OFF HERE
     * @TODO: Determine a way to mock to the responseList object (factory pattern?)
     */
    public function testListMetaDataForamtsReturnsValidArray() 
    {

    }

    // -------------------------------------------------------------------------

    /**
     * Shortcut to load contents of a sample XML file
     */
    protected function getSampleXML($file)
    {
        $ds = DIRECTORY_SEPARATOR;
        return __DIR__ . $ds . 'SampleXML' . $ds . $file;
    }  

    // -------------------------------------------------------------------------

    protected function getMockClient($retVal = null)
    {
        $mock = $this->getMockBuilder('Phpoaipmh\Client')->disableOriginalConstructor()->getMock();
        $mock->expects($this->any())->method('request')->will($this->returnValue($retVal));
        return $mock;
    }
}

/* EndpointTest.php */