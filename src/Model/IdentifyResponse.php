<?php

declare(strict_types=1);

namespace Phpoaipmh\Model;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DOMDocument;
use DOMNode;
use InvalidArgumentException;
use Phpoaipmh\Behavior\RetrieveNodeTrait;
use Phpoaipmh\Granularity;

/**
 * Class IdentifyResponse
 *
 * Refer to: http://www.openarchives.org/OAI/openarchivesprotocol.html#Identify
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class IdentifyResponse
{
    use RetrieveNodeTrait;

    public const DELETED_RECORD_NO = 'no';
    public const DELETED_RECORD_TRANSIENT = 'transient';
    public const DELETED_RECORD_PERSISTENT = 'persistent';

    public const GRANULARITY_DATE = "YYYY-MM-DD";
    public const GRANULARITY_DATE_AND_TIME = "YYYY-MM-DDThh:mm:ssZ";

    /**
     * @var string
     */
    private $repositoryName;

    /**
     * @var string
     */
    private $baseURL;

    /**
     * @var string
     */
    private $protocolVersion;

    /**
     * @var DateTimeImmutable
     */
    private $earliestDatestamp;

    /**
     * @var string
     */
    private $deletedRecordPolicy;

    /**
     * @var Granularity
     */
    private $granularity;

    /**
     * @var array|string[]
     */
    private $adminEmails;

    /**
     * @var string|null
     */
    private $compression;

    /**
     * @var iterable|DOMNode[]
     */
    private $descriptions = [];

    /**
     * @param string $xml
     * @return static
     */
    public static function fromXmlString(string $xml): self
    {
        $doc = new DOMDocument();
        $doc->validateOnParse = true;
        $doc->loadXML($xml);

        // Required attributes
        $repoName = self::retrieveNodeValue($doc, 'repositoryName');
        $baseUrl = self::retrieveNodeValue($doc, 'baseURL');
        $protocolVersion = self::retrieveNodeValue($doc, 'protocolVersion');
        $earliestDatestamp = self::retrieveNodeDateValue($doc, 'earliestDatestamp');
        $deletedRecord = self::retrieveNodeValue($doc, 'deletedRecord');
        $granularity  = new Granularity(self::retrieveNodeValue($doc, 'granularity'));
        $adminEmails = self::retrieveNodeValues($doc, 'adminEmail');

        // OPTIONAL attributes:
        $compression = self::retrieveNodeValue($doc, 'compression', false);
        $descriptions = $doc->getElementsByTagName('description');

        return new static(
            $repoName,
            $baseUrl,
            $protocolVersion,
            $earliestDatestamp,
            $deletedRecord,
            $granularity,
            $adminEmails,
            $compression,
            $descriptions
        );
    }

    /**
     * IdentifyResponse constructor.
     *
     * @param string $repositoryName
     * @param string $baseURL
     * @param string $protocolVersion
     * @param DateTimeInterface|DateTime $earliestDatestamp
     * @param string $deletedRecordPolicy
     * @param Granularity $granularity
     * @param iterable|string[] $adminEmails
     * @param string|null $compression
     * @param iterable|DOMNode[] $descriptions
     */
    public function __construct(
        string $repositoryName,
        string $baseURL,
        string $protocolVersion,
        DateTimeInterface $earliestDatestamp,
        string $deletedRecordPolicy,
        Granularity $granularity,
        iterable $adminEmails = [],
        ?string $compression = null,
        iterable $descriptions = []
    ) {
        $this->repositoryName = $repositoryName;
        $this->granularity = $granularity;
        $this->compression = $compression ?: null;

        $this->baseURL = filter_var($baseURL, FILTER_VALIDATE_URL);
        if (! $this->baseURL) {
            throw new InvalidArgumentException('Invalid value for "baseURL" element');
        }

        // Set protocol version
        if ((int) $protocolVersion !== 2) {
            trigger_error(
                'This library is designed to work with OAI-PMH 2.0 endpoints. YMMV with v' . $protocolVersion,
                E_USER_NOTICE
            );
        }
        $this->protocolVersion = $protocolVersion;

        // Earliest date/time should be immutable
        $this->earliestDatestamp = $earliestDatestamp instanceof DateTimeImmutable
            ? $earliestDatestamp
            : DateTimeImmutable::createFromMutable($earliestDatestamp);

        // Set deleted record policy
        $validPolicies = [self::DELETED_RECORD_NO, self::DELETED_RECORD_PERSISTENT, self::DELETED_RECORD_TRANSIENT];
        if (! in_array($deletedRecordPolicy, $validPolicies)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid deleted record policy value in Identify endpoint: %s (valid values: %s)',
                $deletedRecordPolicy,
                "'" . implode("', '", $validPolicies) . "'"
            ));
        }
        $this->deletedRecordPolicy = $deletedRecordPolicy;

        // Admin emails are strings
        foreach ($adminEmails as $idx => $email) {
            if (!$this->adminEmails[] = filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid email at index %d: %s',
                    $idx,
                    $email
                ));
            }
        }

        // Descriptions are DOMNodes
        foreach ($descriptions as $idx => $desc) {
            if ($desc instanceof DOMNode) {
                $this->descriptions[] = $desc;
            } else {
                throw new InvalidArgumentException(sprintf(
                    "Invalid description at index %d in %s",
                    $idx,
                    self::getXMLDocumentName()
                ));
            }
        }
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
     * @return string
     */
    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    /**
     * @return string
     */
    public function getBaseURL(): string
    {
        return $this->baseURL;
    }

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getEarliestDatestamp(): DateTimeImmutable
    {
        return $this->earliestDatestamp;
    }

    /**
     * @return string
     */
    public function getDeletedRecordPolicy(): string
    {
        return $this->deletedRecordPolicy;
    }

    /**
     * @return Granularity
     */
    public function getGranularity(): Granularity
    {
        return $this->granularity;
    }

    /**
     * @return array|string[]
     */
    public function getAdminEmails(): array
    {
        return $this->adminEmails;
    }

    /**
     * Get the first admin email from the list
     *
     * @return string
     */
    public function getFirstAdminEmail(): string
    {
        return current($this->adminEmails);
    }

    /**
     * @return string|null
     */
    public function getCompression(): ?string
    {
        return $this->compression;
    }

    /**
     * @return DOMNode[] |null
     */
    public function getDescriptions(): iterable
    {
        return $this->descriptions;
    }

    /**
     * Does this endpoint report its compression method?
     *
     * @return bool
     */
    public function hasCompression(): bool
    {
        return (bool) $this->compression;
    }

    /**
     * Does this endpoint include a description of the repository?
     *
     * @return int
     */
    public function getDescriptionCount(): int
    {
        return count($this->descriptions);
    }

    /**
     * Get human-readable name of document for error messages and such
     * @return string
     */
    protected static function getXMLDocumentName(): string
    {
        return 'Identify Response';
    }
}
