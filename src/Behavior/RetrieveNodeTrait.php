<?php
declare(strict_types=1);

namespace Phpoaipmh\Behavior;

use DateTimeImmutable;
use DOMDocument;
use Exception;
use Phpoaipmh\Exception\MalformedResponseException;
use Throwable;

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
     */
    abstract protected static function getXMLDocumentName(): string;

    // ------------------------------------------------------------------------

    /**
     * Retrieve a single node value as a string
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

        return trim($item->item(0)->nodeValue);
    }


    /**
     * Retrieve multiple node values as strings
     *
     * @return array<int,string>
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
            $returnValues[] = trim($node->nodeValue);
        }

        return $returnValues ?? [];
    }

    /**
     * Retrieve a single node value as a date object
     */
    protected static function retrieveNodeDateValue(
        DOMDocument $doc,
        string $nodeName,
        bool $isRequired = true
    ): DateTimeImmutable {
        try {
            return new DateTimeImmutable(static::retrieveNodeValue($doc, $nodeName, $isRequired));
        } catch (MalformedResponseException $e) { // if already MalformedResponseException, then throw it.
            throw $e;
        } catch (Throwable $e) { // else, wrap the exception
            throw new MalformedResponseException(
                sprintf('invalid date value for "%s" in %s', $nodeName, static::getXMLDocumentName()),
                $e->getCode(),
                $e
            );
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
