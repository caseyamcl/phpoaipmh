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
     * @var iterable|Record[]
     */
    private $records = [];

    /**
     * @var ResumptionToken|null
     */
    private $resumptionToken = null;

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
            MalformedResponseException::missingTag('responseDate', 'Record Page');
        }
        $responseDate = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', trim($rdItem->nodeValue));

        // Get the request tag and read attributes into $params variable
        if (! $reqItem = $doc->getElementsByTagName('request')->item(0)) {
            MalformedResponseException::missingTag('request', 'Record Page');
        }
        if (! $verb = $reqItem->attributes->getNamedItem('verb')) {
            MalformedResponseException::missingTag('verb', 'Record Page');
        } else {
            $verb = $verb->value;
        }
        $params = [];
        foreach ($reqItem->attributes as $attrName => $attr) {
            if ($attrName === 'verb') {
                continue;
            } else {
                $params[$attrName] = (string) $attr->value;
            }
        }

        // if this is an error response, handle it here.
        if ($err = $doc->getElementsByTagName('error')->item(0)) {
            $errorCode = (string) $err->attributes->getNamedItem('code')->value;
            $errorMessage = (string) trim($err->nodeValue);
            throw new OaipmhException($errorCode, $errorMessage);
        }

        // otherwise, the next element should match the verb value
        // in which case, iterate through the values and build a Record object for each one
        if ($records = $doc->getElementsByTagName($verb)->item(0)) {
            $records = function (DOMNode $node) {
                yield Record::fromDomNode($mdPrefix, $node);
            };
        } else {
            throw new MalformedResponseException(sprintf(
                'Expected %s element in page record XML document',
                $verb->value
            ));
        }

        $resumptionToken = ($rt = $doc->getElementsByTagName('resumptionToken')->item(0))
            ? ResumptionToken::fromDomNode($rt)
            : null;

        return new static($responseDate, $verb, $records, $resumptionToken, $params);
    }

    /**
     * RecordPage constructor.
     *
     * @param DateTimeInterface|DateTime $responseDate
     * @param string $verb
     * @param iterable $records
     * @param ResumptionToken|null $resumptionToken
     * @param array $params
     */
    public function __construct(
        DateTimeInterface $responseDate,
        string $verb,
        iterable $records,
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
     * @return string
     */
    public function __toString(): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        // TODO: Add elements here.

        return trim($dom->saveXML());
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
     * @param RecordProcessor|null $processor
     * @return iterable
     */
    public function getRecords(RecordProcessor $processor = null): iterable
    {
        // TODO: If processor passed, then process the record; otherwise, return the record iterator
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
     * Get iterator with optional processing
     *
     * If $processor is set, this method returns a generator that prepares the records.
     * Otherwise, it returns 'Model\Record' instances.
     *
     * @param RecordProcessor|null $processor = null
     * @return Traversable|iterable|mixed[]
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