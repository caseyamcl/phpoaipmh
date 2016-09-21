<?php

namespace Phpoaipmh\Model;

/**
 * Created by PhpStorm.
 * User: casey
 * Date: 9/20/16
 * Time: 12:40 PM
 */
class RequestParameters
{
    /**
     * @var string
     */
    private $verb;

    /**
     * @var array
     */
    private $params;

    /**
     * @var string
     */
    private $endpointUrl;

    /**
     * RequestParameters constructor.
     *
     * @param string $endpointUrl
     * @param string $verb
     * @param array  $params
     */
    public function __construct($endpointUrl, $verb, array $params = [])
    {
        $this->verb        = $verb;
        $this->params      = $params;
        $this->endpointUrl = $endpointUrl;
    }

    /**
     * @return string
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Does a parameter exist (excluding 'Verb')?
     *
     * @param string $paramName
     * @return bool
     */
    public function has($paramName)
    {
        return array_key_exists($paramName, $this->params);
    }

    /**
     * Get a custom parameter (excluding 'Verb'; use getVerb())
     *
     * @param string $paramName
     * @return mixed
     */
    public function get($paramName)
    {
        if ($this->has($paramName)) {
            return $this->params[$paramName];
        }
        else {
            throw new \RuntimeException("Parameter does not exist or is not set: " . $paramName);
        }
    }

    /**
     * Get copy of parameters with added or changed parameter
     *
     * @param string $name
     * @param string $value
     * @return static|RequestParameters
     */
    public function withParam($name, $value)
    {
        if ($name == 'Verb') {
            throw new \RuntimeException('Verb cannot be changed');
        }

        $that = clone $this;
        $that->params[$name] = $value;
        return $that;
    }

    /**
     * @return string
     */
    public function getEndpointUrl()
    {
        return $this->endpointUrl;
    }
}
