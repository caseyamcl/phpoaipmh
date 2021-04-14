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

namespace Phpoaipmh;

use PHPUnit_Framework_TestCase;

/**
 * Endpoint Test
 *
 * @package Phpoaipmh
 */
class GranularityTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test the date formatting with granularity
     * @dataProvider getFormatTests
     */
    public function testGranularityFormatting($granularity, $timeZone, $expectedResult)
    {
        $testDate = new \DateTime("2015-02-01 12:15:30", $timeZone);
        $returnValue = Granularity::formatDate($testDate, $granularity);
        $this->assertEquals($expectedResult, $returnValue);
    }

    public function getFormatTests()
    {
        return array(
            array(
              Granularity::DATE,
              new \DateTimeZone("UTC"),
              "2015-02-01"
            ),
            array(
              Granularity::DATE_AND_TIME,
              new \DateTimeZone("UTC"),
              "2015-02-01T12:15:30Z"
            ),
            array(
              Granularity::DATE,
              new \DateTimeZone("Africa/Johannesburg"),
              "2015-02-01"
            ),
            array(
              Granularity::DATE_AND_TIME,
              new \DateTimeZone("Africa/Johannesburg"),
              "2015-02-01T10:15:30Z"
            ),
        );
    }
}
