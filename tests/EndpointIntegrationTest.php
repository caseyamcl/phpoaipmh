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

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Phpoaipmh\HttpAdapter\GuzzleAdapter;
use PHPUnit_Framework_TestCase;

/**
 * Endpoint Guzzle Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class EndpointIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function testGetRecordWorksForCorrectRecord()
    {
        $recordId = 'oai:nsdl.org:2200/20061003062336249T';

        $handler = new MockHandler([
            new Response(
                200,
                ['Content-type' => 'text/xml'],
                fopen(__DIR__ . '/SampleXML/GoodResponseSingleRecord.xml', 'r')
            )
        ]);

        $guzzle = new GuzzleAdapter(new \GuzzleHttp\Client(['handler' => $handler]));
        $endpoint = new Endpoint(new Client('http://example.org', $guzzle));
        $result = $endpoint->getRecord($recordId, 'nsdl_dc');

        $this->assertInstanceOf(\SimpleXMLElement::class, $result);
        $this->assertEquals($recordId, (string) $result->GetRecord->record->header->identifier[0]);
    }

    /**
     * @depends testGetRecordWorksForCorrectRecord
     * @expectedException \Phpoaipmh\Exception\OaipmhException
     */
    public function testGetRecordThrowsOAIExceptionForInvalidRecord()
    {
        $handler = new MockHandler([
            new Response(
                200,
                ['Content-type' => 'text/xml'],
                fopen(__DIR__ . '/SampleXML/BadResponseInvalidRecord.xml', 'r')
            )
        ]);

        $guzzle = new GuzzleAdapter(new \GuzzleHttp\Client(['handler' => $handler]));
        $endpoint = new Endpoint(new Client('http://example.org', $guzzle));
        $result = $endpoint->getRecord('thisISTotalFake', 'nsdl_dc');
    }
}
