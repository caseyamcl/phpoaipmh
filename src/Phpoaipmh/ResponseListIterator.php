<?php

namespace Phpoaipmh;

class ResponseListIterator implements \Iterator {

    /**
     * @var int
     */
    private $totalEntities;

    /**
     * Recordset expiration date, converted to Unixtime
     *
     * @var int
     */
    private $expireDate;

    /** 
     * @var string
     */
    private $resumptionToken;
 
    /**
     * @var int
     */
    private $index = 0;

    /**
     * Array of records
     *
     * @var array
     */
    private $batch;

    // -------------------------------------------------------------------------

    public function __construct($httpClient, $endpoint) {

    }

    // -------------------------------------------------------------------------

    public function current() {
       //return the current item 
    }

    // -------------------------------------------------------------------------

    public function key() {
        return $this->index;
    }

    // -------------------------------------------------------------------------

    public function next() {
        $this->index++;
    }

    // -------------------------------------------------------------------------

    public function rewind() {
        throw new \RuntimeException('Cannot Rewind OAI-PMH Client Iterator');
    }

    // -------------------------------------------------------------------------

    public function valid() {
        //return boolean based on if the current item exists
    }
}

/* EOF: ClientRecordIterator.php */