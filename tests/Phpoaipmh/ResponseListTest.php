<?php

namespace Phpoaipmh;

use Phpoaipmh\Fixture\ClientStub;
use PHPUnit_Framework_TestCase;

/**
 * Response List Test
 *
 * @package Phpoaipmh
 */
class ResponseListTest extends PHPUnit_Framework_TestCase
{
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
        $obj = $this->getSampleMultiPageResponseList();

        while($rec = $obj->nextItem()) {
            $respArr[] = $rec;
        }

        $this->assertEquals(733, count($respArr));
        $this->assertEquals(733, $obj->getNumProcessed());
        $this->assertEquals(4, $obj->getNumRequests());        
    }

    // -------------------------------------------------------------------------

    public function testIteratorWorksWithMultiPageRequest()
    {
        $obj = $this->getSampleMultiPageResponseList();

        $numRecs = 0;
        foreach ($obj as $count => $rec) {
            $numRecs++;
        }

        $this->assertEquals(733, $numRecs);
    }

    // ----------------------------------------------------------------

    public function testIteratorWorksWhenRewound()
    {
        $obj = $this->getSampleMultiPageResponseList();

        // Ensure that we get to record 202
        for ($i = 0; $i < 202; $i++) {
            $currRec = $obj->next();
        }
        $this->assertEquals( 'oai:nsdl.org:2200/20061003062907355T', (string) $currRec->identifier);

        // Now rewind..
        $obj->rewind();

        // ..and ensure the record we get is the first one.
        $currRec = $obj->current();
        $this->assertEquals('oai:nsdl.org:2200/20120614151514710T', (string) $currRec->identifier);

    }


    // -------------------------------------------------------------------------

    /**
     * Test exception thrown if verb used that does not return a list (e.g. 'Identify')
     */
    public function testRequestThrowsExceptionForNonListVerb()
    {
        $this->setExpectedException('RuntimeException');
        $output = $this->generateSampleXML(array('GoodResponseSinglePage.xml'));
        new ResponseList($this->getMockClient($output), 'Identify');
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

    // ----------------------------------------------------------------

    /**
     * @return ResponseList  Consists of 733 records over 4 requests
     */
    protected function getSampleMultiPageResponseList()
    {
        //Multi page sample files contain a total of 733 results in valid ListIdentifiers response
        $output = $this->generateSampleXML([
            'GoodResponseFourPage_1.xml',
            'GoodResponseFourPage_2.xml',
            'GoodResponseFourPage_3.xml',
            'GoodResponseFourPage_4.xml'
        ]);

        return new ResponseList($this->getMockClient($output), 'ListIdentifiers');
    }

    // -------------------------------------------------------------------------

    /**
     * Generate Sample XML results from a OAI-PMH Endpoint
     *
     * @param array $sampleFiles  List of sample files to read from in the SampleXML/ folder
     * @return array Array of SimpleXML Elements
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
     * @param array $toReturn  Array of values to return for consecutive calls (send one for same every time)
     * @return \Phpoaipmh\HttpAdapter\HttpAdapterInterface
     */
    protected function getMockClient($toReturn = array())
    {
        $stub = new ClientStub();
        $stub->retVals = $toReturn;    
        return $stub;
    }
}

// =============================================================================



/* EOF: ResponseListTest.php */