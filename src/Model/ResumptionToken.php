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

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DOMDocument;
use Exception;
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
     * Example string that this method is able to parse:
     *   <resumptionToken completeListSize="733" cursor="0" expirationDate="2099-01-01T01:30:28Z">
     *      0/200/733/nsdl_dc/null/2012-07-26/null
     *   </resumptionToken>
     *
     * @param string $tokenTag  The raw XML tag representing the resumption token
     * @return static
     * @throws MalformedResponseException  In the case that invalid XML data is passed
     */
    public static function fromString(string $tokenTag): self
    {
        try {
            $doc = new DOMDocument();
            $doc->validateOnParse = true;
            $doc->loadXML($tokenTag);

            if (! $element = $doc->getElementsByTagName('resumptionToken')->item(0)) {
                throw new MalformedResponseException("XML is missing expected element: 'resumptionToken'");
            }

            $attrs = $element->attributes;

            if ($attrs->getNamedItem('completeListSize')) {
                $completeListSize = (int) $attrs->getNamedItem('completeListSize')->value;
            }
            if ($attrs->getNamedItem('cursor')) {
                $cursor = (int) $attrs->getNamedItem('cursor')->value;
            }
            if ($attrs->getNamedItem('expirationDate')) {
                $expirationDate = new DateTimeImmutable((string) $attrs->getNamedItem('expirationDate')->value);
            }

            return new static(
                trim($element->nodeValue),
                $completeListSize ?? null,
                $cursor ?? null,
                $expirationDate ?? null
            );
        } catch (MalformedResponseException $e) {
            // pass-through MalFormedResponse exceptions.
            throw $e;
        } catch (Exception $e) {
            // catch all other exceptions and convert them to MalformedResponseExceptions
            throw new MalformedResponseException('Tag parse error: ' . $e->getMessage(), $e->getCode(), $e);
        }
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
