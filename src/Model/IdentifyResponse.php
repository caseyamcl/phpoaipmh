<?php

declare(strict_types=1);

namespace Phpoaipmh\Model;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DOMDocument;
use InvalidArgumentException;

/**
 * Class IdentifyResponse
 *
 * Refer to: http://www.openarchives.org/OAI/openarchivesprotocol.html#Identify
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class IdentifyResponse
{
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
     * @var string
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
     * @var string|null
     */
    private $description;

    /**
     * @param string $xml
     * @return static
     */
    public static function fromXmlString(string $xml): self
    {
        // TODO: Implement this method.
    }

    /**
     * IdentifyResponse constructor.
     * @param string $repositoryName
     * @param string $baseURL
     * @param string $protocolVersion
     * @param DateTimeInterface|DateTime $earliestDatestamp
     * @param string $deletedRecordPolicy
     * @param string $granularity
     * @param array|string[] $adminEmail
     * @param string|null $compression
     * @param string|null $description
     */
    public function __construct(
        string $repositoryName,
        string $baseURL,
        string $protocolVersion,
        DateTimeInterface $earliestDatestamp,
        string $deletedRecordPolicy,
        string $granularity,
        $adminEmail,
        ?string $compression = null,
        ?string $description = null
    ) {
        $this->repositoryName = $repositoryName;
        $this->baseURL = $baseURL;
        $this->adminEmails = is_array($adminEmail) ? array_map('trim', $adminEmail) : [trim($adminEmail)];
        $this->compression = trim($compression) ?: null;
        $this->description = trim($description) ?: null;

        // Set protocol version
        if ((int) $protocolVersion !== 2) {
            trigger_error(
                'Warning: This library is designed to work with OAI-PMH 2.0 endpoints. YMMV with ' . $protocolVersion,
                E_USER_NOTICE
            );
        }
        $this->protocolVersion = $protocolVersion;

        $this->earliestDatestamp = $earliestDatestamp instanceof DateTimeImmutable
            ? $earliestDatestamp
            : DateTimeImmutable::createFromMutable($earliestDatestamp);

        // Set deleted record policy
        $validPolicies = [self::DELETED_RECORD_NO, self::DELETED_RECORD_PERSISTENT, self::DELETED_RECORD_TRANSIENT];
        if (! in_array($deletedRecordPolicy, $validPolicies)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid deleted record policy in Identify endpoint: %s (valid values: %s)',
                $deletedRecordPolicy,
                $validPolicies
            ));
        }
        $this->deletedRecordPolicy = $deletedRecordPolicy;

        // Set date/time granularity
        if (! in_array($granularity, [self::GRANULARITY_DATE, self::GRANULARITY_DATE_AND_TIME])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid granularity: %s (valid values: %s)',
                $granularity,
                [self::GRANULARITY_DATE, self::GRANULARITY_DATE_AND_TIME]
            ));
        }
        $this->granularity = $granularity;
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
     * @return string
     */
    public function getGranularity(): string
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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Does this endpoint describe its compression method
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
     * @return bool
     */
    public function hasDescription(): bool
    {
        return (bool) $this->description;
    }
}
