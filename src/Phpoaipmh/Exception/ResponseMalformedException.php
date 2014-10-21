<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 10/21/14
 * Time: 3:35 PM
 */

namespace Phpoaipmh\Exception;

/**
 * Class ResponseMalformedException
 *
 * Thrown when the HTTP response body cannot be parsed into valid OAI-PMH (usually XML errors)
 */
class ResponseMalformedException extends BaseOaipmhException
{
    // pass..
}

/* EOF: ResponseMalformedException.php */ 