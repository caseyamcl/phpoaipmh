<?php
/**
 * phpoaipmh
 *
 * @license ${LICENSE_LINK}
 * @link ${PROJECT_URL_LINK}
 * @version ${VERSION}
 * @package ${PACKAGE_NAME}
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, -please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Phpoaipmh\Fixture;

use Phpoaipmh\HttpAdapter\HttpAdapterInterface;

/**
 * Mock HTTP Client
 *
 * Keeps track of requests
 *
 * @author Christian Scheb
 */
class HttpMockClient implements HttpAdapterInterface
{
    /**
     * @var string
     */
    public $lastRequestUrl;

    /**
     * @var string
     */
    public $toReturn = '';

    // ---------------------------------------------------------------

    /**
     * Request
     *
     * @param string $url
     * @return string
     */
    public function request($url)
    {
        $this->lastRequestUrl = $url;
        return $this->toReturn;
    }

    // ---------------------------------------------------------------


    /**
     * @return string
     */
    public function getLastRequestUrl()
    {
        return $this->lastRequestUrl;
    }
}
