<?php

declare(strict_types=1);

namespace Phpoaipmh\Processor;

use Exception;
use Phpoaipmh\Exception\MalformedResponseException;
use Phpoaipmh\Exception\OaipmhException;
use RuntimeException;
use SimpleXMLElement;

/**
 * Class SimpleXMLProcessor
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class SimpleXMLProcessor /* TODO: Create ProcessorInterface */
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