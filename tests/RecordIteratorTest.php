<?php

declare(strict_types=1);

namespace Phpoaipmh;

use DateTimeInterface;
use Phpoaipmh\HttpAdapter\GuzzleAdapter;

/**
 * Created by PhpStorm.
 * User: casey
 * Date: 10/15/18
 * Time: 1:27 PM
 */

class RecordIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClientReturnsClientInstance()
    {
        $iterator = new \Phpoaipmh\RecordIterator($this->singlePageRequestClient(), 'ListRecords');
        $this->assertInstanceOf(\Phpoaipmh\ClientInterface::class, $iterator->getClient());
    }

    public function testGetResumptionTokenReturnsNullWhenNoTokenExists()
    {
        $iterator = new \Phpoaipmh\RecordIterator($this->singlePageRequestClient(), 'ListRecords');
        $iterator->next();
        $this->assertNull($iterator->getResumptionToken());
    }

    public function testGetResumptionTokenReturnsTokenWhenTokenExists()
    {
        $iterator = new \Phpoaipmh\RecordIterator($this->multiplePageRequestClient(), 'ListIdentifiers');
        $iterator->next();
        $this->assertNotNull($iterator->getResumptionToken());
    }

    public function testGetExpirationDateReturnsNullWhenNoDataExists()
    {
        $iterator = new \Phpoaipmh\RecordIterator($this->singlePageRequestClient(), 'ListRecords');
        $iterator->next();
        $this->assertNull($iterator->getExpirationDate());
    }

    public function testGetExpirationDateReturnsDateTimeWhenDataExists()
    {
        $iterator = new \Phpoaipmh\RecordIterator($this->multiplePageRequestClient(), 'ListIdentifiers');
        $iterator->next();
        $this->assertInstanceOf(DateTimeInterface::class, $iterator->getExpirationDate());
    }

    public function testKeyCallsNextItemWhenCurrItemIsNull()
    {
        $iterator = new \Phpoaipmh\RecordIterator($this->multiplePageRequestClient(), 'ListIdentifiers');
        $key = $iterator->key();
        $this->assertInternalType('int', $key);
    }

    /**
     * @return Client
     */
    protected function singlePageRequestClient()
    {
        $handler = new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(
                200,
                ['Content-type' => 'text/xml'],
                fopen(__DIR__ . '/SampleXML/GoodResponseSinglePage.xml', 'r')
            )
        ]);

        return new Client('http://example.org', new GuzzleAdapter(new \GuzzleHttp\Client(['handler' => $handler])));
    }

    /**
     * @return Client
     */
    protected function multiplePageRequestClient()
    {
        $handler = new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(
                200,
                ['Content-type' => 'text/xml'],
                fopen(__DIR__ . '/SampleXML/GoodResponseFourPage_1.xml', 'r')
            )
        ]);

        return new Client('http://example.org', new GuzzleAdapter(new \GuzzleHttp\Client(['handler' => $handler])));
    }
}
