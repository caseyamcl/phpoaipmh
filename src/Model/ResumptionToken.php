<?php

declare(strict_types=1);

namespace Phpoaipmh\Model;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Phpoaipmh\Exception\MalformedResponseException;

/**
 * Resumption Token class
 *
 * See: https://www.openarchives.org/OAI/openarchivesprotocol.html#FlowControl
 * TODO: Create test for this
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ResumptionToken
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var int|null
     */
    private $completeListSize = null;

    /**
     * @var int|null
     */
    private $cursor = null;

    /**
     * @var DateTimeImmutable
     */
    private $expirationDate = null;

    /**
     * Build class from XML string
     *
     * @param string $tokenTag  The raw XML tag representing the resumption token
     * @return static
     * @throws MalformedResponseException  In the case that invalid XML data is passed
     */
    public static function fromString(string $tokenTag): self
    {
        // TODO: LEFT OFF HERE.. I think the XMLRPC extension is bundled with PHP, but be sure before you use it...
        xmlrpc_decode('test');
    }

    /**
     * ResumptionToken constructor.
     *
     * @param string $token
     * @param int|null $completeListSize
     * @param int|null $cursor
     * @param DateTimeInterface|DateTime|null $expirationDate
     */
    public function __construct(
        string $token,
        ?int $completeListSize = null,
        ?int $cursor = null,
        ?DateTimeInterface $expirationDate = null
    ) {
        $this->token = $token;
        $this->completeListSize = $completeListSize;
        $this->cursor = $cursor;

        if ($expirationDate) {
            $this->expirationDate = ($expirationDate instanceof DateTimeImmutable)
                ? $expirationDate
                : DateTimeImmutable::createFromMutable($expirationDate);
        }
    }

    /**
     * Get the resumption token string
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Get string representation of resumption token
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->token;
    }

    /**
     * Get the complete list size, if is specified
     *
     * @return int|null
     */
    public function getCompleteListSize(): ?int
    {
        return $this->completeListSize;
    }

    /**
     * Get the current cursor, if it is specified
     *
     * @return int|null
     */
    public function getCursor(): ?int
    {
        return $this->cursor;
    }

    /**
     * Get the expiration date for this record set, if it is specified
     *
     * The expiration date represents the date/time after which the resumptionToken ceases to be valid
     *
     * @return DateTimeImmutable|null
     */
    public function getExpirationDate(): ?DateTimeImmutable
    {
        return $this->expirationDate;
    }

    /**
     * Does this token include the optional complete list size parameter?
     *
     * @return bool
     */
    public function hasCompleteListSize(): bool
    {
        return (bool) $this->completeListSize;
    }

    /**
     * Does this token include the optional expiration date parameter?
     *
     * @return bool
     */
    public function hasExpirationDate(): bool
    {
        return (bool) $this->expirationDate;
    }

    /**
     * Does this token include the optional cursor parameter?
     * @return bool
     */
    public function hasCursor(): bool
    {
        return (bool) $this->cursor;
    }

    /**
     * If this token has an expiration date, it can be used to check if the token is still valid
     *
     * @return bool|null  Returns TRUE if the token is still valid, FALSE if it is not, or NULL if undetectable
     */
    public function isValid(): ?bool
    {
        return $this->hasExpirationDate() ? (time() <= $this->getExpirationDate()->format('U')) : null;
    }
}
