<?php

namespace Phpoaipmh\Fixture;

use Phpoaipmh\Client;

/**
 * Client Stub
 *
 * @package Phpoaipmh\Fixture
 */
class ClientStub extends Client
{
    /**
     * @var array  Array of values to return
     */
    public $retVals = array();

    /**
     * @var int
     */
    private $callNum = 0;

    // ----------------------------------------------------------------

    public function __construct()
    {
        // override parent by doing nothing.
    }

    // ----------------------------------------------------------------

    public function request($url, array $params = array())
    {
        // Only increment the call number if there is a resumption token
        $this->callNum = (isset($params['resumptionToken']))
            ? $this->callNum + 1
            : 0;

        // Get the page from the array that represents the request page we are on
        $toReturn = (isset($this->retVals[$this->callNum]))
            ? $this->retVals[$this->callNum]
            : null;

        return $toReturn;
    }
}

/* EOF: ClientStub.php */