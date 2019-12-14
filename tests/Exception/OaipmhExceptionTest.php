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

namespace Phpoaipmh\Exception;

class OaipmhExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testToStringReturnsExpectedValue()
    {
        $obj = new OaipmhException('12345', 'OAI Error Message', 1);
        $this->assertEquals('OaipmhException: [1]: (12345) OAI Error Message', (string) $obj);
    }
}
