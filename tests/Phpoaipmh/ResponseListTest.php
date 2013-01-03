<?php

namespace Phpoaipmh;
use PHPUnit_Framework_TestCase;

class ResponseListTest extends PHPUnit_Framework_TestCase
{

    // -------------------------------------------------------------------------

    /**
     * Simple Instantiation Test
     *
     * Tests that no syntax or runtime errors occur during object insantiation
     */
    public function testInsantiateCreatesNewObject()
    {    
        $obj = new ResponseList($this->getMockClient(), 'ListIdentifiers');
        $this->assertInstanceOf('Phpoaipmh\ResponseList', $obj);
    }

    // -------------------------------------------------------------------------

    /**
     * Test single page request
     */
    public function testSinglePageRequestGeneratesValidOutput()
    {
        //Single page sample file contains 162 results in a valid ListRecords response
        $output = $this->generateSampleXML(array('GoodResponseSinglePage.xml'));
        $obj = new ResponseList($this->getMockClient($output), 'ListRecords');

        while($rec = $obj->nextItem()) {
            $respArr[] = $rec;
        }

        $this->assertEquals(162, count($respArr));
        $this->assertEquals(162, $obj->getNumProcessed());
        $this->assertEquals(1, $obj->getNumRequests());
    }

    // -------------------------------------------------------------------------

    /**
     * Test Multipage Request with Valid Output
     */
    public function testMultiPageRequestGeneratesValidOutput()
    {
        //Multi page sample files contain a total of 733 results in valid ListIdentifiers response
        $output = $this->generateSampleXML(array(
            'GoodResponseFourPage_1.xml',
            'GoodResponseFourPage_2.xml',
            'GoodResponseFourPage_3.xml',
            'GoodResponseFourPage_4.xml'
        ));
        $obj = new ResponseList($this->getMockClient($output), 'ListIdentifiers');


        while($rec = $obj->nextItem()) {
            $respArr[] = $rec;
        }

        $this->assertEquals(733, count($respArr));
        $this->assertEquals(733, $obj->getNumProcessed());
        $this->assertEquals(4, $obj->getNumRequests());        
    }

    // -------------------------------------------------------------------------

    /**
     * Test exception thrown if verb used that does not return a list (e.g. 'Identify')
     */
    public function testRequestThrowsExceptionForNonListVerb()
    {
        $this->setExpectedException('RuntimeException');
        $output = $this->generateSampleXML(array('GoodResponseSinglePage.xml'));
        $obj = new ResponseList($this->getMockClient($output), 'Identify');
    }

    // -------------------------------------------------------------------------

    /**
     * Test exception thrown if expected XML structure does not exist for a verb
     *
     * To test, we are using a sample 'ListRecords' response for a 'ListIdentifiers'
     * request, which will generate the exception
     */
    public function testRequestThrowsExceptionForMissingSchemaForVerb()
    {
        $this->setExpectedException('RuntimeException');
        $output = $this->generateSampleXML(array('GoodResponseSinglePage.xml'));
        $obj = new ResponseList($this->getMockClient($output), 'ListIdentifiers');
        $obj->nextItem();        
    }

    // -------------------------------------------------------------------------

    /**
     * Generate Sample XML results from a OAI-PMH Endpoint
     *
     * @param array $sampleFiles
     * List of sample files to read from in the SampleXML/ folder
     *
     * @return array
     * Array of SimpleXML Elements
     */
    protected function generateSampleXML($sampleFiles)
    {
        foreach ($sampleFiles as $file) {

            $obj = simplexml_load_file(__DIR__ . '/../fixtures/SampleXML/' . $file);

            if ( ! $obj) {
                user_error(sprintf("Could not load sampel XML file: %s", $file));
            }

            $outArr[] = $obj;
        }

        return $outArr;
    }

    // -------------------------------------------------------------------------

    /**
     * Get a mock client
     *
     * @param array $toReturn
     * Array of values to return for consecutive calls (send one for same every time)
     *
     * @return Phpoaipmh\Client
     * Mocked Phpoaipmh Client
     */
    protected function getMockClient($toReturn = array())
    {
        $stub = new MockClient();
        $stub->retVals = $toReturn;    
        return $stub;
    }
}

// =============================================================================

class MockClient extends Client
{
    public $retVals = array();
    private $callNum = 0;

    public function __construct() {
        //pass//
    }

    public function request($url)
    {
        $toReturn = (isset($this->retVals[$this->callNum]))
            ? $this->retVals[$this->callNum]
            : null;

        $this->callNum++;
        return $toReturn;
    }
}


/* EOF: ResponseListTest.php */