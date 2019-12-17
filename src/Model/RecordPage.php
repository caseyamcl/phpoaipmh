<?php

declare(strict_types=1);

namespace Phpoaipmh\Model;

use Countable;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use IteratorAggregate;

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
     * @return array|string[]
     */
    public function getRecords()
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
     * @inheritDoc
     */
    public function getIterator()
    {
        // TODO: Implement getIterator() method.
    }

    /**
     * Count the number of records on this page
     */
    public function count(): int
    {
        // TODO: Implement count() method.
    }
}