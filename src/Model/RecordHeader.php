<?php

declare(strict_types=1);

namespace Phpoaipmh\Model;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DOMDocument;

class RecordHeader
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var DateTimeImmutable
     */
    private $datestamp;

    /**
     * @var array|string[]
     */
    private $setSpec = [];

    /**
     * @var string|null
     */
    private $status = null;

    /**
     * RecordHeader constructor.
     * @param string $identifier
     * @param DateTimeInterface|DateTime $datestamp
     * @param array|string[] $setSpec
     * @param string|null $status
     */
    public function __construct(
        string $identifier,
        DateTimeInterface $datestamp,
        array $setSpec = [],
        ?string $status = null
    ) {
        $this->identifier = $identifier;
        $this->datestamp = $datestamp;
        $this->setSpec = $setSpec;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        // Strip header XML tag from generated XML for the resumptionToken
        $xml = $this->getDomRepresentation()->saveXML();
        return trim(str_replace('<?xml version="1.0"?>', '', $xml));
    }

    /**
     * Get the XML Document Object Model (DOM) Representation
     *
     * @return DOMDocument
     */
    public function getDomRepresentation(): DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        // TODO: Add elements here.

        return $dom;
    }

}