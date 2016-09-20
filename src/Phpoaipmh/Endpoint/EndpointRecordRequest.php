<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 9/20/16
 * Time: 2:43 PM
 */

namespace Phpoaipmh\Endpoint;

use Phpoaipmh\ClientInterface;
use Phpoaipmh\Model\Record;
use Phpoaipmh\Model\RequestParameters;

/**
 * Class EndpointRecordRequest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class EndpointRecordRequest
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestParameters
     */
    private $parameters;

    /**
     * EndpointRecordRequest constructor.
     *
     * @param ClientInterface   $client
     * @param RequestParameters $parameters
     */
    public function __construct(ClientInterface $client, RequestParameters $parameters)
    {
        $this->client     = $client;
        $this->parameters = $parameters;
    }

    /**
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return RequestParameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Add/change a parameter
     *
     * @param string $name
     * @param string $value
     * @return $this|EndpointRecordRequest
     */
    public function withParameter($name, $value)
    {
        $this->parameters = $this->parameters->withParam($name, $value);
        return $this;
    }

    /**
     * @return Record
     */
    public function run()
    {
        return $this->client->getRecord($this->parameters);
    }
}
