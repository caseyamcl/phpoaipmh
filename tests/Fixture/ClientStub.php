<?php

/**
 * PHPOAIPMH Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/phpoaipmh
 * @version 3.0
 * @package caseyamcl/phpoaipmh
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, -please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Phpoaipmh\Fixture;

use Phpoaipmh\Client;

/**
 * Client Stub
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
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
