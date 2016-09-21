<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 9/20/16
 * Time: 8:54 PM
 */

namespace Phpoaipmh\Model;


class RequestParametersTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateCreatesNewObject()
    {
        $obj = new RequestParameters('http://www.example.org', 'Identify');
        $this->assertInstanceOf('Phpoaipmh\Model\RequestParameters', $obj);
    }

    public function testGetVerb()
    {
        $obj = new RequestParameters('http://www.example.org', 'Identify');
        $this->assertEquals('Identify', $obj->getVerb());
    }

    public function testGetParamsEmpty()
    {
        $obj = new RequestParameters('http://www.example.org', 'Identify');
        $this->assertEquals([], $obj->getParams());
    }

    public function testGetParamsFull()
    {
        $obj = new RequestParameters('http://www.example.org', 'Identify', ['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $obj->getParams());
    }

    public function testHasParamOnlyChecksCustomParams()
    {
        $obj = new RequestParameters('http://www.example.org', 'Identify', ['foo' => 'bar']);
        $this->assertTrue($obj->has('foo'));
        $this->assertFalse($obj->has('Verb'));
    }

    public function testGetParamsGetsCustomParams()
    {
        $obj = new RequestParameters('http://www.example.org', 'Identify', ['foo' => 'bar']);
        $this->assertEquals('bar', $obj->get('foo'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetParamsThrowsExceptionForNonCustomParam()
    {
        $obj = new RequestParameters('http://www.example.org', 'Identify', ['foo' => 'bar']);
        $obj->get('Verb'); // should not work
    }

    public function testGetEndpointUrl()
    {
        $obj = new RequestParameters('http://www.example.org', 'Identify', ['foo' => 'bar']);
        $this->assertEquals('http://www.example.org', $obj->getEndpointUrl());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testWithParamThrowsExceptionIfVerbPassed()
    {
        $obj = new RequestParameters('http://www.example.org', 'Identify', ['foo' => 'bar']);
        $obj->withParam('Verb', 'ListItems');
    }

    public function testWithParamReturnsNewObjectWithAddedParam()
    {
        $obj = new RequestParameters('http://www.example.org', 'Identify', ['foo' => 'bar']);
        $new = $obj->withParam('baz', 'biz');

        $this->assertEquals(['foo' => 'bar'], $obj->getParams());
        $this->assertEquals(['foo', 'baz'], array_keys($new->getParams()));
    }
}
