<?php

declare(strict_types=1);

namespace Phpoaipmh\Model;

use DOMDocument;

class Record
{
    /**
     * @var RecordHeader
     */
    private $header;

    /**
     * @var RecordMetadata
     */
    private $metadata;

    /**
     * @var array
     */
    private $about = [];


    /**
     * @return string
     */
    public function __toString(): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        $dom->appendChild($this->header->getDomRepresentation());
        $dom->appendChild($this->metadata->getDomRepresentation());

        foreach ($this->about as $aboutSection) {
            $dom->appendChild($aboutSection->getDomRepresentation());
        }


        // Strip header XML tag from generated XML for the resumptionToken
        $xml = $dom->saveXML();
        return trim(str_replace('<?xml version="1.0"?>', '', $xml));
    }

    /**
     * @return RecordHeader
     */
    public function getHeader(): RecordHeader
    {
        return $this->header;
    }

    /**
     * @return RecordMetadata
     */
    public function getMetadata(): RecordMetadata
    {
        return $this->metadata;
    }

    /**
     * @return array|null
     */
    public function getAbout(): ?array
    {
        return $this->about;
    }
}