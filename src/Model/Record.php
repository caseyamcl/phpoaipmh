<?php

declare(strict_types=1);

namespace Phpoaipmh\Model;

use DOMDocument;
use DOMNode;

/**
 * A single OAI-PMH record
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Record
{
    private string $header;
    private ?string $metadata;
    private string $format;

    /**
     * @var array<int,string>
     */
    private array $about = [];


    /**
     * @param string $metadataPrefix
     * @param DOMNode $node
     * @return static
     */
    public static function fromDomNode(string $metadataPrefix, DOMNode $node): self
    {
        // TODO: finish this...
        die('left off here');
    }

    /**
     * Record constructor.
     * @param string $format  Format of the record
     * @param string $header XML for the header
     * @param string|null $metadata XML for the metadata, if present
     * @param iterable|string[] $about XML for the about sections, if present
     */
    public function __construct(string $format, string $header, ?string $metadata, iterable $about)
    {
        $this->format = $format;
        $this->header = $header;
        $this->metadata = $metadata;
        $this->about = $about;
    }

    public function __toString(): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        // Strip header XML tag from generated XML for the resumptionToken
        $xml = $dom->saveXML();
        return trim(str_replace('<?xml version="1.0"?>', '', $xml));
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getMetadata(): string
    {
        return $this->metadata;
    }

    /**
     * @return array<int,string>|null
     */
    public function getAbout(): ?array
    {
        return $this->about;
    }
}
