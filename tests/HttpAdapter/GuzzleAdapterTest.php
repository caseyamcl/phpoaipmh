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

namespace Phpoaipmh\Http;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Phpoaipmh\HttpAdapter\GuzzleAdapter;
use PHPUnit_Framework_TestCase;

/**
 * Class GuzzleAdapterTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class GuzzleAdapterTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! interface_exists('\GuzzleHttp\ClientInterface')) {
            $this->markTestSkipped('Guzzle is not installed.  Skipping GuzzleAdapter tests.');
        }
        elseif ((int) substr(ClientInterface::VERSION, 0, 1) < 6) {
            $this->markTestSkipped(sprintf(
                'Guzzle is at version %s.  Skipping GuzzleAdapter tests.',
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
    public function testInsantiateCreatesNewObject()
    {
        $obj = new GuzzleAdapter();
        $this->assertInstanceOf('Phpoaipmh\HttpAdapter\GuzzleAdapter', $obj);
    }

    /**
     * Simple URL Call Test - Will fail with no internet connectivity
     *
     * Also tests 301 redirects, since http://example.org will redirect
     */
    public function testGoodRequestReturnsContentBody()
    {
        $obj      = new GuzzleAdapter($this->getGuzzleObject(200, 'good'));
        $response = $obj->request('http://example.org');

        $this->assertContains('good', $response);
    }

    public function testGetGuzzleClientReturnsGuzzleClientObject()
    {
        $obj = new GuzzleAdapter();
        $this->assertInstanceOf('\GuzzleHttp\Client', $obj->getGuzzleClient());
    }

    private function getGuzzleObject($code = 200, $responseBody = 'good-stuff')
    {
        // Create a mock and queue response
        $mock = new MockHandler([
            new Response($code, [], $responseBody),
        ]);

        $handler = HandlerStack::create($mock);
        return new Client(['handler' => $handler]);
    }
}
