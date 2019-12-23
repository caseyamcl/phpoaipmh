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

namespace Phpoaipmh\Processor;

use Exception;
use Phpoaipmh\Contract\RecordProcessor;
use Phpoaipmh\Exception\MalformedResponseException;
use Phpoaipmh\Exception\OaipmhException;
use RuntimeException;
use SimpleXMLElement;

/**
 * Class SimpleXMLProcessor
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class SimpleXMLProcessor implements RecordProcessor
{
    /**
     * SimpleXMLProcessor constructor.
     *
     * Checks for presence of SimpleXML extension
     */
    public function __construct()
    {
        if (! class_exists(SimpleXMLElement::class)) {
            throw new RuntimeException(sprintf(
                'Please install the SimpleXML extension in order to use the %s processor',
                get_called_class()
            ));
        }
    }

    /**
     * @param string $recordData
     * @return SimpleXMLElement
     */
    public function process(string $recordData)
    {
        //Setup a SimpleXML Document
        try {
            $xml = @new SimpleXMLElement($recordData);
        } catch (Exception $e) {
            throw new MalformedResponseException(sprintf("Could not decode XML Response: %s", $e->getMessage()));
        }

        //If we get back a OAI-PMH error, throw a OaipmhException
        if (isset($xml->error)) {
            $code = (string) $xml->error['code'];
            $msg  = (string) $xml->error;

            throw new OaipmhException($code, $msg);
        }

        return $xml;
    }
}