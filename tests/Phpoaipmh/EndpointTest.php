<?php

/**
 * PHPOAIPMH Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/phpoaipmh
 * @version 2.0
 * @package caseyamcl/phpoaipmh
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace Phpoaipmh;

use PHPUnit_Framework_TestCase;

/**
 * Endpoint Test
 *
 * @package Phpoaipmh
 */
class EndpointTest extends PHPUnit_Framework_TestCase
{

    // -------------------------------------------------------------------------

    /**
     * Simple Instantiation Test
     *
     * Tests that no syntax or runtime errors occur during object insantiation
     */
    public function testInsantiateCreatesNewObject()
    {    
        $obj = new Endpoint($this->getMockClient());
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
        $obj = new Endpoint($client);
        $response = $obj->identify();

        //Check results
        $this->assertInstanceof('SimpleXMLElement', $response);
        $this->assertObjectHasAttribute('Identify', $response);
    }

    // -------------------------------------------------------------------------

    /**
     * Test that list MetaDataFormats resturns valid array
     */
    public function testListMetaDataFormatsReturnsValidArray() 
    {

    }

    // -------------------------------------------------------------------------

    /**
     * Shortcut to load contents of a sample XML file
     */
    protected function getSampleXML($file)
    {
        return __DIR__ . '/../fixtures/SampleXML/' . $file;
    }  

    // -------------------------------------------------------------------------

    protected function getMockClient($retVal = null)
    {
        $mock = $this->getMockBuilder('Phpoaipmh\Client')->disableOriginalConstructor()->getMock();
        $mock->expects($this->any())->method('request')->will($this->returnValue($retVal));
        return $mock;
    }
}

/* EOF: EndpointTest.php */