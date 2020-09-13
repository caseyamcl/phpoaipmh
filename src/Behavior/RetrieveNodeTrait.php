<?php
declare(strict_types=1);

namespace Phpoaipmh\Behavior;

use DateTimeImmutable;
use DOMDocument;
use Exception;
use Phpoaipmh\Exception\MalformedResponseException;

/**
 * Class RetrieveNodeTrait
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
trait RetrieveNodeTrait
{
    /**
     * All classes implementing this trait need to identify a human-friendly XML Document Name
     *
     * Examples:
     *
     *  - 'Identify Response'
     *  - 'Record Page'
     *
     * @return string
     */
    abstract protected static function getXMLDocumentName(): string;

    // ------------------------------------------------------------------------

    /**
     * Retrieve a single node value as a string
     *
     * @param DOMDocument $doc
     * @param string $nodeName
     * @param bool $isRequired
     * @return string|null
     */
    protected static function retrieveNodeValue(DOMDocument $doc, string $nodeName, bool $isRequired = true): ?string
    {
        $item = $doc->getElementsByTagName($nodeName);

        if ($item->count() === 0) {
            if ($isRequired) {
                static::generateException($nodeName);
            } else {
                return null;
            }
        }

        if ($item->count() > 1) {
            user_error(sprintf(
                'node element "%s" contains more than a single element (%s) in %s',
                $nodeName,
                $item->count(),
                static::getXMLDocumentName()
            ));
        }

        return $item->item(0)->nodeValue;
    }


    /**
     * Retrieve multiple node values as strings
     *
     * @param DOMDocument $doc
     * @param string $nodeName
     * @param bool $isRequired
     * @return array
     */
    protected static function retrieveNodeValues(DOMDocument $doc, string $nodeName, bool $isRequired = true): array
    {
        $domNodes = $doc->getElementsByTagName($nodeName);

        if ($domNodes->count() === 0) {
            if ($isRequired) {
                static::generateException($nodeName);
            } else {
                return [];
            }
        }

        foreach ($domNodes as $node) {
            $returnValues[] = $node->nodeValue;
        }

        return $returnValues ?? [];
    }

    /**
     * Retrieve a single node value as a date object
     *
     * @param DOMDocument $doc
     * @param string $nodeName
     * @param bool $isRequired
     * @return DateTimeImmutable
     */
    protected static function retrieveNodeDateValue(
        DOMDocument $doc,
        string $nodeName,
        bool $isRequired = true
    ): DateTimeImmutable {
        try {
            return new DateTimeImmutable(static::retrieveNodeValue($doc, $nodeName, $isRequired));
        } catch (Exception $e) {
            if ($e instanceof MalformedResponseException) {
                throw $e;
            } else {
                throw new MalformedResponseException(
                    sprintf('invalid date value for "%s" in %s', $nodeName, static::getXMLDocumentName()),
                    $e->getCode(),
                    $e
                );
            }
        }
    }

    /**
     * Throw exception based on node name
     *
     * @param string $nodeName
     */
    private static function generateException(string $nodeName)
    {
        throw MalformedResponseException::missingTag($nodeName, static::getXMLDocumentName());
    }
}
