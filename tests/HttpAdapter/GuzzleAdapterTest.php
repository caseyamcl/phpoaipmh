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

namespace Phpoaipmh\Http;

use Phpoaipmh\HttpAdapter\GuzzleAdapter;
use PHPUnit_Framework_TestCase;

/**
 * Class GuzzleAdapterTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class GuzzleAdapterTest extends PHPUnit_Framework_TestCase
{
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

    // -------------------------------------------------------------------------

    /**
     * Simple URL Call Test - Will fail with no internet connectivity
     *
     * Also tests 301 redirects, since http://example.org will redirect
     */
    public function testGoodRequestReturnsContentBody()
    {
        $obj = new GuzzleAdapter();
        $res = $obj->request('http://example.org');

        $this->assertTrue(
            strpos($res, "<body>") != false,
            "The response should include a <body> tag, since it is a HTML document"
        );
    }

    // ----------------------------------------------------------------

    public function testGetGuzzleClientReturnsGuzzleClientObject()
    {
        $obj = new GuzzleAdapter();
        $this->assertInstanceOf('\GuzzleHttp\Client', $obj->getGuzzleClient());
    }

    // -------------------------------------------------------------------------

    /**
     * Tests that a non-existent resource (HTTP 404) throws an exception
     */
    public function test404ResponseThrowsAnException()
    {
        $this->setExpectedException('Phpoaipmh\Exception\HttpException');

        $obj = new GuzzleAdapter();
        $obj->request('http://w3.org/doesnotexistyo');
    }

    // -------------------------------------------------------------------------

    /**
     * Tests that a non-existent server throws an exception
     */
    public function testNonExistentServerThrowsException()
    {
        $this->setExpectedException('Phpoaipmh\Exception\HttpException');

        $obj = new GuzzleAdapter();
        $obj->request('http://doesnotexist.blargasdf');
    }
}
