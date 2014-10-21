<?php

namespace Phpoaipmh;

use Phpoaipmh\Exception\OaipmhException;

/**
 * Class OaipmhRequestException
 *
 * @package Phpoaipmh
 */
class OaipmhRequestException extends OaipmhException
{
    private $oaiErrorCode;

    // -------------------------------------------------------------------------

    public function __construct($oaiErrorCode, $message, $code = 0, \Exception $previous = null)
    {
        $this->oaiErrorCode = $oaiErrorCode;
        parent::__construct($message, $code, $previous);
    }

    // -------------------------------------------------------------------------

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: ({$this->oaiErrorCode}) {$this->message}\n";
    }
}

/* OaipmhRequestException.php */