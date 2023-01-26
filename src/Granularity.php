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
use Phpoaipmh\Exception\MalformedResponseException;

/**
 * Granularity class provides utility for specifying date and constraint precision
 *
 * @author Christian Scheb
 */
class Granularity
{
    public const DATE = "YYYY-MM-DD";
    public const DATE_AND_TIME = "YYYY-MM-DDThh:mm:ssZ";

    private string $format;

    /**
     * Automatically try to create format for date
     *
     * @param DateTimeInterface $dt
     * @return string
     */
    public static function forDate(DateTimeInterface $dt): string
    {
        $format = ($dt->format('H:i:s') === '00:00:00') ? static::DATE : static::DATE_AND_TIME;
        return (new static($format))->formatDate($dt);
    }

    /**
     * Granularity constructor.
     * @param string $format  One of DATE, or DATE_AND_TIME constants
     */
    public function __construct(string $format)
    {
        $allowedValues = [self::DATE, self::DATE_AND_TIME];
        if (! in_array($format, $allowedValues)) {
            throw new MalformedResponseException(
                sprintf('OAI-PMH endpoint returned invalid granularity: %s (allowed %s)', $format, $allowedValues)
            );
        }

        $this->format = $format;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->format;
    }

    /**
     * Format DateTime string based on granularity
     *
     * @param DateTimeInterface $dateTime
     * @return string
     */
    public function formatDate(DateTimeInterface $dateTime): string
    {
        $formatMapping = [
            self::DATE => "Y-m-d",
            self::DATE_AND_TIME => 'Y-m-d\TH:i:s\Z'
        ];

        return $dateTime->format($formatMapping[$this->format]);
    }
}
