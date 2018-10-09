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

/**
 * DateGranularity class provides utility for specifying date and constraint precision
 *
 * See: http://www.openarchives.org/OAI/openarchivesprotocol.html#Dates
 *
 * @author Christian Scheb
 */
class DateGranularity
{
    const DATE          = "YYYY-MM-DD";
    const DATE_AND_TIME = "YYYY-MM-DDThh:mm:ssZ";

    /**
     * @var array
     */
    private static $mapping = [
        self::DATE          => "Y-m-d",
        self::DATE_AND_TIME => 'Y-m-d\TH:i:s\Z'
    ];

    /**
     * @var string
     */
    private $format;

    /**
     * Construct DateGranularity object for Date
     *
     * @return static
     */
    public static function date()
    {
        return new static(static::DATE);
    }

    /**
     * Construct DateGranularity object for DateAndTime
     *
     * @return static
     */
    public static function dateAndTime()
    {
        return new static(static::DATE_AND_TIME);
    }

    /**
     * DateGranularity constructor.
     *
     * @param string $format
     */
    public function __construct($format)
    {
        if (! in_array($format, [self::DATE, self::DATE_AND_TIME])) {
            throw new \InvalidArgumentException(sprintf(
                "Invalid granularity format (must be one of %s or %s)",
                get_called_class() . '::DATE',
                get_called_class() . '::DATE_AND_TIME'
            ));
        }

        $this->format = $format;
    }

    /**
     * @param \DateTimeInterface $dateTime
     * @return string
     */
    public function formatDate(\DateTimeInterface $dateTime)
    {
        $phpFormat = static::$mapping[$this->format];
        return $dateTime->format($phpFormat);
    }

    /**
     * @param $dateString
     * @return \DateTime
     */
    public function createDateTimeObject($dateString)
    {
        return \DateTime::createFromFormat(
            static::$mapping[$this->format],
            $dateString
        );
    }
}
