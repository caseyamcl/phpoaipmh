<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 10/21/14
 * Time: 3:35 PM
 */

namespace Phpoaipmh\Exception;

/**
 * Class MalformedResponseException
 *
 * Thrown when the HTTP response body cannot be parsed into valid OAI-PMH (usually XML errors)
 */
class MalformedResponseException extends BaseOaipmhException
{
    // pass..
}

/* EOF: MalformedResponseException.php */