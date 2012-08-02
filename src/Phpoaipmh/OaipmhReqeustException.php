<?php

namespace Phpoaipmh;

class OaipmhReqeustException extends \Exception {

    private $oaiErrorCode;

    public function __construct($oaiErrorCode, $message, $code = 0, Exception $previous = null)
    {
        $this->oaiErrorCode = $oaiErrorCode;
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: ({$this->oaiErrorCode}) {$this->message}\n";
    }


}

/* OaipmhRequestException.php */