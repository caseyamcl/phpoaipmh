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

declare(strict_types=1);

namespace Phpoaipmh;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Endpoint Test
 *
 * @package Phpoaipmh
 */
class GranularityTest extends TestCase
{
    /**
     * Test static method with date
     */
    public function testForDateWhenValueIsDate(): void
    {
        $dt = new DateTimeImmutable('2010-01-01');
        $this->assertEquals('2010-01-01', Granularity::forDate($dt));
    }

    /**
     * Test static method with datetime
     */
    public function testForDateTimeWhenValueIsDateTime(): void
    {
        $dt = new DateTimeImmutable('2010-01-01 14:30:23');
        $this->assertEquals('2010-01-01T14:30:23Z', Granularity::forDate($dt));
    }

    /**
     * Test the date formatting with granularity
     *
     * @dataProvider getFormatTests
     * @param string $granularity
     * @param string $expectedResult
     * @throws Exception
     */
    public function testGranularityFormatting(string $granularity, string $expectedResult): void
    {
        $obj = new Granularity($granularity);
        $testDate = new DateTime("2015-02-01 12:15:30", new DateTimeZone("UTC"));
        $this->assertEquals($expectedResult, $obj->formatDate($testDate));
    }

    public function getFormatTests(): array
    {
        return [
            [Granularity::DATE, "2015-02-01"],
            [Granularity::DATE_AND_TIME, "2015-02-01T12:15:30Z"],
        ];
    }
}
