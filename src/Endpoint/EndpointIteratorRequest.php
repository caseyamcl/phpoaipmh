<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 9/20/16
 * Time: 2:47 PM
 */

namespace Phpoaipmh\Endpoint;

/**
 * Class EndpointIteratorRequest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class EndpointIteratorRequest extends EndpointRecordRequest
{
    /**
     * @return \Traversable|\SimpleXMLElement[]
     */
    public function run()
    {
        return $this->getClient()->iterateRecords($this->getParameters());
    }

}
