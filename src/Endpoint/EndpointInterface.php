<?php

/**
 * PHPOAIPMH Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/phpoaipmh
 * @version 2.0
 * @package caseyamcl/phpoaipmh
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace Phpoaipmh\Endpoint;

use Phpoaipmh\ClientInterface;

/**
 * OAI-PMH Endpoint Interface
 *
 * @since  v2.3
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
interface EndpointInterface
{
    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return ClientInterface
     */
    public function getClient();

    /**
     * Identify the OAI-PMH Endpoint
     *
     * @return EndpointRecordRequest   A request to get a single record
     */
    public function identify();

    /**
     * List Metadata Formats
     *
     * Return the list of supported metadata format for a particular record (if
     * $identifier is provided), or the entire repository (if no arguments are
     * provided)
     *
     * @param  string $identifier         If specified, will return only those
     *                                    metadata formats that a particular
     *                                    record supports
     * @return EndpointIteratorRequest   A request to get multiple records
     */
    public function listMetadataFormats($identifier = null);

    /**
     * List Record Sets
     *
     * @return EndpointIteratorRequest   A request to get multiple records
     */
    public function listSets();

    /**
     * Get a single record
     *
     * @param  string $id             Record Identifier
     * @param  string $metadataPrefix Required by OAI-PMH endpoint
     * @return EndpointRecordRequest  A request to get a single record
     */
    public function getRecord($id, $metadataPrefix);

    /**
     * List Record identifiers
     *
     * Corresponds to OAI Verb to list record identifiers
     *
     * @param  string             $metadataPrefix      Required by OAI-PMH endpoint
     * @param  \DateTimeInterface $from                An optional 'from' date for
     *                                                 selective harvesting
     * @param  \DateTimeInterface $until               An optional 'until' date for
     *                                                 selective harvesting
     * @param  string             $set                 An optional setSpec for selective
     *                                                 harvesting
     * @param  string             $resumptionToken     An optional resumptionToken for selective
     *                                                 harvesting
     * @return EndpointIteratorRequest   A request to get multiple records
     */
    public function listIdentifiers($metadataPrefix, \DateTimeInterface $from = null, \DateTimeInterface $until = null, $set = '', $resumptionToken = '');

    /**
     * List Records
     *
     * Corresponds to OAI Verb to list records
     *
     * @param  string             $metadataPrefix      Required by OAI-PMH endpoint
     * @param  \DateTimeInterface $from                An optional 'from' date for
     *                                                 selective harvesting
     * @param  \DateTimeInterface $until               An optional 'from' date for
     *                                                 selective harvesting
     * @param  string             $set                 An optional setSpec for selective
     *                                                 harvesting
     * @param  string             $resumptionToken     An optional resumptionToken for selective
     *                                                 harvesting
     * @return EndpointIteratorRequest   A request to get multiple records
     */
    public function listRecords($metadataPrefix, \DateTimeInterface $from = null, \DateTimeInterface $until = null, $set = '', $resumptionToken = '');
}
