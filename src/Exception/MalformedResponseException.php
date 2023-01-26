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
 * Class MalformedResponseException
 *
 * Thrown when the HTTP response body cannot be parsed into valid OAI-PMH (usually XML errors)
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @since v2.0
 */
class MalformedResponseException extends BaseOaipmhException
{
    /**
     * Alternative constructor to create errors about missing tags
     *
     * @param string $tagName
     * @param string $responseDocName
     * @param int $code
     * @param Throwable|null $previous
     * @return MalformedResponseException
     */
    public static function missingTag(
        string $tagName,
        string $responseDocName,
        int $code = 0,
        ?Throwable $previous = null
    ): self {
        return new self(
            sprintf("Expected '%s' element in %s XML document", $tagName, $responseDocName),
            $code,
            $previous
        );
    }

    public static function missingAttribute(
        string $attributeName,
        string $tagName,
        string $responseDocName,
        int $code = 0,
        ?Throwable $previous = null
    ): self {
        $msg = sprintf('Missing %s attribute of %s tag in %s XML document', $attributeName, $tagName, $responseDocName);
        return new self($msg, $code, $previous);
    }
}
