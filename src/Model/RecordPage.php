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

use ArrayIterator;
use Countable;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DOMDocument;
use DOMNode;
use IteratorAggregate;
use Phpoaipmh\Contract\RecordProcessor;
use Phpoaipmh\Exception\MalformedResponseException;
use Phpoaipmh\Exception\OaipmhException;
use Traversable;

/**
 * Record Page
 *
 * Represents a single XML document returned by an HTTP endpoint
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @since v4.0
 */
class RecordPage implements IteratorAggregate, Countable
{
    private const PAGE_TYPE = 'record page';

    private string $sourceXml;
    private DateTimeImmutable $responseDate;
    private ?ResumptionToken $resumptionToken = null;
    private string $verb;

    /**
     * @var array<int,string>
     */
    private array $params = [];

    /**
     * @var iterable<int,Record>
     */
    private iterable $records = [];

    /**
     * Build class from XML string
     *
     * This method expects a full XML document containing a complete, valid OAI-PMH
     * document.  Anything else throws a MalformedResponseException.
     *
     * @param string $xml
     * @return static
     * @throws MalformedResponseException|OaipmhException
     */
    public static function fromXmlString(string $xml): self
    {
        $doc = new DOMDocument();
        $doc->validateOnParse = true;
        $doc->loadXML($xml);

        // Get the response date
        if (! $rdItem = $doc->getElementsByTagName('responseDate')->item(0)) {
            MalformedResponseException::missingTag('responseDate', self::PAGE_TYPE);
        }
        $responseDate = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', trim($rdItem->nodeValue));

        // Get the request tag and read attributes into $params variable
        if (! $reqItem = $doc->getElementsByTagName('request')->item(0)) {
            MalformedResponseException::missingTag('request', self::PAGE_TYPE);
        }
        if (! $verb = $reqItem->attributes->getNamedItem('verb')) {
            MalformedResponseException::missingTag('verb', self::PAGE_TYPE);
        } else {
            $verb = $verb->value;
        }
        $requestParams = [];
        foreach ($reqItem->attributes as $attrName => $attr) {
            if ($attrName === 'verb') {
                continue;
            } else {
                $requestParams[$attrName] = trim($attr->value);
            }
        }

        // if this is an error response, handle it here.
        if ($err = $doc->getElementsByTagName('error')->item(0)) {
            $errorCode = (string) $err->attributes->getNamedItem('code')->value;
            $errorMessage = trim($err->nodeValue);
            throw new OaipmhException($errorCode, $errorMessage);
        }

        // Get the resumptionToken
        $resumptionToken = ($rt = $doc->getElementsByTagName('resumptionToken')->item(0))
            ? ResumptionToken::fromDomNode($rt)
            : null;

        if (! $mdPrefix = $requestParams['metadataPrefix']) {
            // TODO: allow $mdPrefix to be set by parsing the resumption token (if set) using a custom callback method.
            // See: https://www.openarchives.org/OAI/openarchivesprotocol.html#ListRecords

            // for now, though, just raise an error
            throw MalformedResponseException::missingAttribute('metadataPrefix', 'request', self::PAGE_TYPE);
        }

        // If not an error response, the next element should match the verb value
        // in which case, iterate through the values and build a Record object for each one
        if (! $records = $doc->getElementsByTagName($verb)->item(0)->childNodes) {
            throw new MalformedResponseException(sprintf(
                'Expected %s element in page record XML document',
                $verb->value
            ));
        }

        return new static($xml, $responseDate, $verb, $records, $resumptionToken, $requestParams);
    }

    /**
     * RecordPage constructor.
     *
     * @param string $sourceXml
     * @param DateTimeInterface|DateTime $responseDate
     * @param string $verb
     * @param iterable $records
     * @param ResumptionToken|null $resumptionToken
     * @param array $params
     */
    public function __construct(
        string $sourceXml,
        DateTimeInterface $responseDate,
        string $verb,
        iterable $records,
        ?ResumptionToken $resumptionToken = null,
        array $params = []
    ) {
        $this->sourceXml = $sourceXml;
        $this->responseDate = $responseDate instanceof DateTimeImmutable
            ? $responseDate
            : DateTimeImmutable::createFromMutable($responseDate);

        $this->verb = $verb;
        $this->records = $records;
        $this->params = $params;
        $this->resumptionToken = $resumptionToken;
    }

    public function __toString(): string
    {
        return $this->sourceXml;
    }

    public function getResponseDate(): DateTimeImmutable
    {
        return $this->responseDate;
    }

    public function getVerb(): string
    {
        return $this->verb;
    }

    /**
     * @return array<int,string>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function getRecords(RecordProcessor $processor = null): iterable
    {
        // TODO: If processor passed, then process the record; otherwise, return the record iterator
        return $this->records;
    }

    public function getResumptionToken(): ?ResumptionToken
    {
        return $this->resumptionToken;
    }

    /**
     * Get iterator with optional processing
     *
     * If $processor is set, this method returns a generator that prepares the records.
     * Otherwise, it returns 'Model\Record' instances.
     */
    public function getIterator(?RecordProcessor $processor = null): Traversable
    {
        if ($processor) {
            foreach ($this->records as $record) {
                yield $processor->process((string) $record);
            }
        } else {
            return new ArrayIterator($this->records);
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