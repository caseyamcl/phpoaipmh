<?php

declare(strict_types=1);

namespace Phpoaipmh\Model;

use DateTimeImmutable;
use IteratorAggregate;

/**
 * Record Page
 *
 * Represents a single XML document returned by an HTTP endpoint
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @since v4.0
 */
class RecordPage implements IteratorAggregate
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
     * @inheritDoc
     */
    public function getIterator()
    {
        // TODO: Implement getIterator() method.
    }
}