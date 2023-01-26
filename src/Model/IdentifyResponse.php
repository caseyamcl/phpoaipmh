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

    private string $sourceXml;
    private string $repositoryName;
    private string $baseURL;
    private string $protocolVersion;
    private DateTimeImmutable $earliestDatestamp;
    private string $deletedRecordPolicy;
    private Granularity $granularity;

    /**
     * @var array<int,string>
     */
    private array $adminEmails;
    private ?string $compression;

    /**
     * @var iterable<int,DOMNode>
     */
    private iterable $descriptions = [];

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
            $xml,
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
     * @param string $sourceXML
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
        string $sourceXML,
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
        $this->sourceXml = $sourceXML;
        $this->repositoryName = $repositoryName;
        $this->granularity = $granularity;
        $this->compression = $compression ?: null;

        $this->baseURL = filter_var($baseURL, FILTER_VALIDATE_URL) ?: '';
        if ($this->baseURL === '') {
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

    public function __toString(): string
    {
        return $this->sourceXml;
    }

    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    public function getBaseURL(): string
    {
        return $this->baseURL;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function getEarliestDatestamp(): DateTimeImmutable
    {
        return $this->earliestDatestamp;
    }

    public function getDeletedRecordPolicy(): string
    {
        return $this->deletedRecordPolicy;
    }

    public function getGranularity(): Granularity
    {
        return $this->granularity;
    }

    public function getAdminEmails(): array
    {
        return $this->adminEmails;
    }

    /**
     * Get the first admin email from the list
     */
    public function getFirstAdminEmail(): string
    {
        return current($this->adminEmails);
    }

    public function getCompression(): ?string
    {
        return $this->compression;
    }

    /**
     * @return iterable<int,DOMNode>
     */
    public function getDescriptions(): iterable
    {
        return $this->descriptions;
    }

    /**
     * Does this endpoint report its compression method?
     */
    public function hasCompression(): bool
    {
        return (bool) $this->compression;
    }

    /**
     * Does this endpoint include one or more descriptions of the repository?
     */
    public function getDescriptionCount(): int
    {
        return count($this->descriptions);
    }

    /**
     * Get human-readable name of document for error messages and such
     */
    protected static function getXMLDocumentName(): string
    {
        return 'Identify Response';
    }
}
