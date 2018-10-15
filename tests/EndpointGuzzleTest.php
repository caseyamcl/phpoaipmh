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

require_once __DIR__ . '/EndpointCurlTest.php';

/**
 * Endpoint Guzzle Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class EndpointGuzzleTest extends EndpointCurlTest
{
    protected function getHttpAdapterObj()
    {
        return new HttpAdapter\GuzzleAdapter();
    }
}
