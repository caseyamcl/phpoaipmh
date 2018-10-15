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
 * For the full copyright and license information, -please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace Phpoaipmh;

use Phpoaipmh\Exception\HttpException;
use Phpoaipmh\Fixture\HttpMockClient;
use PHPUnit_Framework_TestCase;

/**
 * Class ClientTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    // -------------------------------------------------------------------------

    /**
     * Simple Instantiation Test
     *
     * Tests that no syntax or runtime errors occur during object insantiation
     */
    public function testIntantiateCreatesNewObject()
    {
        $obj = new Client('http://example.com/oai', new HttpMockClient);
        $this->assertInstanceOf('Phpoaipmh\Client', $obj);
    }

    // ----------------------------------------------------------------

    public function testInstantiateCreatesNewObjectWhenNoConstructorArgsPassed()
    {
        if (! function_exists('curl_exec') && ! class_exists('\GuzzleHttp\Client')) {
            $this->markTestSkipped("Skipping this test, because both CURL and Guzzle are missing on this system");
        }

        $obj = new Client('http://example.com/oai');
        $this->assertInstanceOf('Phpoaipmh\Client', $obj);
    }

    // -------------------------------------------------------------------------

    /**
     * Test that URL is built correctly
     * @dataProvider getUrlsToTest
     */
    public function testRequestUrlBuildCorrectly($endpointUrl, $expectedRequestUrl)
    {
        $mockClient = new HttpMockClient;
        $mockClient->toReturn = file_get_contents(__DIR__ . '/SampleXML/GoodResponseIdentify.xml');
        $obj = new Client($endpointUrl, $mockClient);
        $obj->request('Identify', array('param' => 'value'));

        $this->assertEquals($expectedRequestUrl, $mockClient->getLastRequestUrl());
    }

    public function getUrlsToTest()
    {
        return array(
            array('http://example.com/oai', 'http://example.com/oai?verb=Identify&param=value'),
            array('http://example.com/?oai=1', 'http://example.com/?oai=1&verb=Identify&param=value'),
        );
    }

    // -------------------------------------------------------------------------

    /**
     * Test that a simple valid response is decoded correctly
     */
    public function testRequestDecodesValidResponseCorrectly()
    {
        $mockClient = new HttpMockClient;
        $mockClient->toReturn = file_get_contents(__DIR__ . '/SampleXML/GoodResponseIdentify.xml');
        $obj = new Client('http://nsdl.org/oai', $mockClient);
        $result = $obj->request('Identify');

        //Check correct object type and result
        $this->assertInstanceOf('SimpleXMLElement', $result);
        $this->assertTrue(isset($result->Identify));
    }

    // -------------------------------------------------------------------------

    /**
     * Test that the client throws a HttpAdapter\HttpException for non-XML or non-parsable responses
     */
    public function testInvalidXMLResponseThrowsHttpRequestException()
    {
        $mockClient = new HttpMockClient;
        $mockClient->toReturn = 'thisIs&NotXML!!';
        $this->setExpectedException('Phpoaipmh\Exception\MalformedResponseException');

        $obj = new Client('http://nsdl.org/oai', $mockClient);
        $obj->request('Identify');
    }

    // -------------------------------------------------------------------------

    /**
     * Test that a XML response with a OAI-PMH error embedded throws an OaipmhException
     */
    public function testRequestThrowsOAIPMHExceptionForInvalidVerbOrParams()
    {
        $mockClient = new HttpMockClient;
        $mockClient->toReturn = file_get_contents(__DIR__ . '/SampleXML/BadResponseNonExistentVerb.xml');
        $this->setExpectedException('Phpoaipmh\Exception\OaipmhException');

        $obj = new Client('http://nsdl.org/oai', $mockClient);
        $obj->request('NonexistentVerb');
    }

    /**
     * Test that a HTTP error response with a OAI-PMH error in body throws an OaipmhException
     */
    public function testHttpErrorStatusWithOaipmhErrorResponseThrowsOAIPMHException()
    {
        $response = file_get_contents(__DIR__ . '/SampleXML/BadResponseNonExistentVerb.xml');
        $httpException = new HttpException($response, "Not found.", 404);

        $mockAdapter = $this->getMock("Phpoaipmh\HttpAdapter\HttpAdapterInterface");
        $mockAdapter
            ->expects($this->any())
            ->method("request")
            ->will($this->throwException($httpException));

        $this->setExpectedException('Phpoaipmh\Exception\OaipmhException');

        $obj = new Client('http://nsdl.org/oai', $mockAdapter);
        $obj->request('NonexistentVerb');
    }

    /**
     * Test that a HTTP error response without a OAI-PMH error in body throws an HttpException
     */
    public function testHttpErrorStatusMissingOaipmhErrorResponseThrowsHttpException()
    {
        $response = 'thisIs&NotXML!!';
        $httpException = new HttpException($response, "Not found.", 404);

        $mockClient = $this->getMock("Phpoaipmh\HttpAdapter\HttpAdapterInterface");
        $mockClient
            ->expects($this->any())
            ->method("request")
            ->will($this->throwException($httpException));

        $this->setExpectedException('Phpoaipmh\Exception\HttpException');

        $obj = new Client('http://nsdl.org/oai', $mockClient);
        $obj->request('Identify');
    }

    // ----------------------------------------------------------------

    public function testRequestThrowsExceptionIfUrlNotSet()
    {
        $this->setExpectedException('\RuntimeException');

        $obj = new Client();
        $obj->request('Identify');
    }
}
