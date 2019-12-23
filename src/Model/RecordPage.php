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

namespace Phpoaipmh\Model;

use Countable;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DOMDocument;
use IteratorAggregate;
use Phpoaipmh\Contract\RecordProcessor;
use Traversable;

/**
 * Record Page
 *
 * Represents a single XML document returned by an HTTP endpoint
 * TODO: Create test for this
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @since v4.0
 */
class RecordPage implements IteratorAggregate, Countable
{
    /**
     * @var DateTimeImmutable
     */
    private $responseDate;

    /**
     * @var string
     */
    private $verb;

    /**
     * @var array|string[]
     */
    private $params = [];

    /**
     * @var array|string[]
     */
    private $records = [];

    /**
     * @var ResumptionToken|null
     */
    private $resumptionToken = null;

    /**
     * RecordPage constructor.
     *
     * @param DateTimeInterface|DateTime $responseDate
     * @param string $verb
     * @param array $records
     * @param ResumptionToken|null $resumptionToken
     * @param array $params
     */
    public function __construct(
        DateTimeInterface $responseDate,
        string $verb,
        array $records,
        ?ResumptionToken $resumptionToken = null,
        array $params = []
    ) {
        $this->responseDate = $responseDate instanceof DateTimeImmutable
            ? $responseDate
            : DateTimeImmutable::createFromMutable($responseDate);

        $this->verb = $verb;
        $this->records = $records;
        $this->params = $params;
        $this->resumptionToken = $resumptionToken;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        // TODO: Add elements here.

        return trim($dom->saveXML());
    }

    /**
     * @return DateTimeImmutable
     */
    public function getResponseDate(): DateTimeImmutable
    {
        return $this->responseDate;
    }

    /**
     * @return string
     */
    public function getVerb(): string
    {
        return $this->verb;
    }

    /**
     * @return array|string[]
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param RecordProcessor|null $processor
     * @return iterable|
     */
    public function getRecords(RecordProcessor $processor = null): iterable
    {
        return $this->records;
    }

    /**
     * @return ResumptionToken|null
     */
    public function getResumptionToken(): ?ResumptionToken
    {
        return $this->resumptionToken;
    }

    /**
     * Get iterator with optional processing
     *
     * If $processor is set, this method returns a generator that prepares the records.
     * Otherwise, it returns 'Model\Record' instances.
     *
     * @param RecordProcessor|null $processor = null
     * @return Traversable|iterable|mixed[]
     */
    public function getIterator(?RecordProcessor $processor = null): Traversable
    {
        if ($processor) {
            foreach ($this->records as $record) {
                yield $processor->process($record);
            }
        } else {
            return new \ArrayIterator($this->records);
        }
    }

    /**
     * Count the number of records on this page
     */
    public function count(): int
    {
        return count($this->records);
    }
}