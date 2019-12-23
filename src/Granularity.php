<?php

/**
 * PHPOAIPMH Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/phpoaipmh
 * @Version 4.0
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

use DateTimeInterface;

/**
 * Granularity class provides utility for specifying date and constraint precision
 *
 * @author Christian Scheb
 */
class Granularity
{
    const DATE = "YYYY-MM-DD";
    const DATE_AND_TIME = "YYYY-MM-DDThh:mm:ssZ";

    /**
     * Format DateTime string based on granularity
     *
     * @param DateTimeInterface $dateTime
     * @param string|null $format  Either self::DATE or self::DATE_AND_TIME; if not specified, will attempt to guess
     *
     * @return string
     */
    public static function formatDate(DateTimeInterface $dateTime, ?string $format = null): string
    {
        if ($format) {
            $phpFormats = [self::DATE => "Y-m-d", self::DATE_AND_TIME => 'Y-m-d\TH:i:s\Z'];
            $phpFormat = $phpFormats[$format];
        } else {
            // attempt to guess based on the date/time object
            $phpFormat = ($dateTime->format('H:i:s') === '00:00:00') ? 'Y-m-d' : 'Y-m-d\TH:i:s\Z';
        }

        return $dateTime->format($phpFormat);
    }
}
