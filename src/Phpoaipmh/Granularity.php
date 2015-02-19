<?php
namespace Phpoaipmh;

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

class Granularity {

    const DATE = "YYYY-MM-DD";
    const DATE_AND_TIME = "YYYY-MM-DDThh:mm:ssZ";

    /**
     * Format DateTime string based on granularity
     *
     * @param \DateTime $dateTime
     * @param string $format
     *
     * @return string
     */
    public static function formatDate(\DateTime $dateTime, $format)
    {
        $phpFormats = array(
            self::DATE => "Y-m-d",
            self::DATE_AND_TIME => \DateTime::ISO8601,
        );
        $phpFormat = $phpFormats[$format];

        return $dateTime->format($phpFormat);
    }
}
