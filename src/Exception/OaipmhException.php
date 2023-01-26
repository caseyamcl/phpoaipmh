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

namespace Phpoaipmh\Exception;

use Throwable;

/**
 * OAI-PMH protocol Exception Class thrown when OAI-PMH protocol errors occur
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @since v2.0
 */
class OaipmhException extends BaseOaipmhException
{
    private string $oaiErrorCode;

    /**
     * OaipmhException constructor.
     * @param string         $oaiErrorCode
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $oaiErrorCode, string $message, int $code = 0, ?Throwable $previous = null)
    {
        $this->oaiErrorCode = $oaiErrorCode;
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return "OaiPmhException: [{$this->code}]: ({$this->oaiErrorCode}) {$this->message}";
    }

    public function getOaiErrorCode(): string
    {
        return $this->oaiErrorCode;
    }
}
