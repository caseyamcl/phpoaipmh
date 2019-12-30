<?php

declare(strict_types=1);

namespace Phpoaipmh\Model;

use DOMDocument;
use DOMNode;
use LogicException;

class Record
{
    /**
     * @var string
     */
    private $header;

    /**
     * @var string|null
     */
    private $metadata;

    /**
     * @var array|string[]
     */
    private $about = [];

    /**
     * @var string
     */
    private $format;

    /**
     * @param DOMNode $node
     * @return static
     */
    public static function fromDomNode(DOMNode $node): self
    {
        throw new LogicException('left off here');
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

    /**
     * @return string
     */
    public function __toString(): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        // Strip header XML tag from generated XML for the resumptionToken
        $xml = $dom->saveXML();
        return trim(str_replace('<?xml version="1.0"?>', '', $xml));
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getMetadata(): string
    {
        return $this->metadata;
    }

    /**
     * @return array|string[]|null
     */
    public function getAbout(): ?array
    {
        return $this->about;
    }
}
