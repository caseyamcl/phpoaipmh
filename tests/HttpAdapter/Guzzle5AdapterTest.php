<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 9/20/16
 * Time: 8:18 PM
 */

namespace Phpoaipmh\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;
use Phpoaipmh\HttpAdapter\Guzzle5Adapter;

/**
 * Class Guzzle5AdapterTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Guzzle5AdapterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! interface_exists('\GuzzleHttp\ClientInterface')) {
            $this->markTestSkipped('Guzzle is not installed.  Skipping GuzzleAdapter tests.');
        }
        elseif ((int) substr(ClientInterface::VERSION, 0, 1) != 5) {
            $this->markTestSkipped(sprintf(
                'Guzzle is at version %s.  Skipping Guzzle5Adapter tests.',
                ClientInterface::VERSION
            ));
        }
    }

    /**
     * Simple Instantiation Test
     *
     * Tests that no syntax or runtime errors occur during object insantiation,
     * and that the class implements the HttpAdapter\HttpAdapterInterface interface
     */
    public function testInstantiateCreatesNewObject()
    {
        $obj = new Guzzle5Adapter();
        $this->assertInstanceOf('Phpoaipmh\HttpAdapter\Guzzle5Adapter', $obj);
    }

    /**
     * Simple URL Call Test - Will fail with no internet connectivity
     *
     * Also tests 301 redirects, since http://example.org will redirect
     */
    public function testGoodRequestReturnsContentBody()
    {
        $obj      = new Guzzle5Adapter($this->getGuzzleObject(200, 'response'));
        $response = $obj->request('http://example.org');

        $this->assertContains('response', $response);
    }

    /**
     *
     */
    public function testGetGuzzleClientReturnsGuzzleClientObject()
    {
        $obj = new Guzzle5Adapter();
        $this->assertInstanceOf('\GuzzleHttp\Client', $obj->getGuzzleClient());
    }

    private function getGuzzleObject($code = 200, $responseBody = 'good-stuff')
    {
        $client = new Client();

        $responseMessage = sprintf(
            "HTTP/1.1 %s RESP\r\n\r\n%s",
            $code,
            $responseBody
        );

        // Create a mock subscriber and queue two responses.
        $mock = new Mock([$responseMessage]);

        // Add the mock subscriber to the client.
        $client->getEmitter()->attach($mock);

        return $client;
    }
}
