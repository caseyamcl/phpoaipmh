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

namespace Phpoaipmh;

use DateTimeInterface;
use InvalidArgumentException;
use SimpleXMLElement;

/**
 * OAI-PMH Endpoint Class
 *
 * @since v1.0
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Endpoint implements EndpointInterface
{
    const AUTO = null;

    /**
     * @var string
     */
    private $granularity;

    /**
     * Build endpoint using URL and default settings
     *
     * @param string $url
     * @return static
     */
    public static function build(string $url): self
    {
        // TODO: Re-implement this
    }

    /**
     * Constructor
     *
     * @param string $granularity  Optional; the OAI date format for fetching records, uses constants from Granularity
     */
    public function __construct(string $granularity = self::AUTO)
    {
        // TODO: Re-implement this
        $this->granularity = $granularity;
    }

    /**
     * Identify the OAI-PMH Endpoint
     *
     * @return SimpleXMLElement A XML document with attributes describing the repository
     */
    public function identify(): SimpleXMLElement
    {
        return $this->client->request('Identify');
    }

    /**
     * List Metadata Formats
     *
     * Return the list of supported metadata format for a particular record (if $identifier
     * is provided), or the entire repository (if no arguments are provided)
     *
     * @param  string  $identifier If specified, will return only those metadata formats that a
     *                             particular record supports
     * @return RecordIteratorInterface
     */
    public function listMetadataFormats(string $identifier = ''): RecordIteratorInterface
    {
        $params = ($identifier) ? array('identifier' => $identifier) : array();

        return new RecordIterator($this->client, 'ListMetadataFormats', $params);
    }

    /**
     * List Record Sets
     *
     * @return RecordIteratorInterface
     */
    public function listSets(): RecordIteratorInterface
    {
        return new RecordIterator($this->client, 'ListSets');
    }

    /**
     * Get a single record
     *
     * @param  string            $id             Record Identifier
     * @param  string            $metadataPrefix Required by OAI-PMH endpoint
     * @return SimpleXMLElement An XML document corresponding to the record
     */
    public function getRecord(string $id, string $metadataPrefix): SimpleXMLElement
    {
        $params = array(
            'identifier'     => $id,
            'metadataPrefix' => $metadataPrefix
        );

        return $this->client->request('GetRecord', $params);
    }

    /**
     * List Record identifiers
     *
     * Corresponds to OAI Verb to list record identifiers
     *
     * @param  string             $metadataPrefix Required by OAI-PMH endpoint
     * @param  DateTimeInterface $from             An optional 'from' date for selective harvesting
     * @param  DateTimeInterface $until            An optional 'until' date for selective harvesting
     * @param  string             $set              An optional setSpec for selective harvesting
     * @param  string             $resumptionToken  An optional resumptionToken for selective harvesting
     * @return RecordIteratorInterface
     */
    public function listIdentifiers(
        string $metadataPrefix,
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $until = null,
        string $set = '',
        $resumptionToken = ''
    ): RecordIteratorInterface {
        return $this->createRecordIterator("ListIdentifiers", $metadataPrefix, $from, $until, $set, $resumptionToken);
    }

    /**
     * List Records
     *
     * Corresponds to OAI Verb to list records
     *
     * @param  string             $metadataPrefix Required by OAI-PMH endpoint
     * @param  DateTimeInterface $from             An optional 'from' date for selective harvesting
     * @param  DateTimeInterface $until            An optional 'from' date for selective harvesting
     * @param  string             $set              An optional setSpec for selective harvesting
     * @param  string             $resumptionToken  An optional resumptionToken for selective harvesting
     * @return RecordIteratorInterface
     */
    public function listRecords(
        string $metadataPrefix,
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $until = null,
        string $set = '',
        string $resumptionToken = ''
    ): RecordIteratorInterface {
        return $this->createRecordIterator("ListRecords", $metadataPrefix, $from, $until, $set, $resumptionToken);
    }

    /**
     * Create a record iterator
     *
     * @param  string            $verb             OAI Verb
     * @param  string            $metadataPrefix   Required by OAI-PMH endpoint
     * @param  DateTimeInterface $from             An optional 'from' date for selective harvesting
     * @param  DateTimeInterface $until            An optional 'from' date for selective harvesting
     * @param  string            $set              An optional setSpec for selective harvesting
     * @param  string            $resumptionToken  An optional resumptionToken for selective harvesting
     *
     * @return RecordIteratorInterface
     */
    private function createRecordIterator(
        string $verb,
        string $metadataPrefix,
        ?DateTimeInterface $from,
        ?DateTimeInterface $until,
        string $set = '',
        string $resumptionToken = ''
    ): RecordIteratorInterface {
        $params = array('metadataPrefix' => $metadataPrefix);
        $params['from'] = Granularity::formatDate($from, $this->getGranularity());
        $params['until'] = Granularity::formatDate($until, $this->getGranularity());

        if ($set) {
            $params['set'] = $set;
        }

        return new RecordIterator($this->client, $verb, $params, $resumptionToken);
    }

    /**
     * Lazy load granularity from Identify, if not specified
     *
     * @return string
     */
    private function getGranularity()
    {
        // If the granularity is not specified, attempt to retrieve it from the server
        // Fall back on DATE granularity
        if ($this->granularity === null) {
            $response = $this->identify();
            return (isset($response->Identify->granularity))
                ? (string) $response->Identify->granularity
                : Granularity::DATE;
        }

        return $this->granularity;
    }
}
