<?php

declare(strict_types=1);

namespace Phpoaipmh\Model;

use DOMDocument;

class RecordMetadata
{
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