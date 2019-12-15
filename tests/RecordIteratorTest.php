<?php

declare(strict_types=1);

namespace Phpoaipmh;

use DateTimeInterface;
use Exception;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Phpoaipmh\HttpAdapter\GuzzleAdapter;
use PHPUnit\Framework\TestCase;

class RecordIteratorTest extends TestCase
{
    public function testGetClientReturnsClientInstance()
    {
        $iterator = new RecordIterator($this->singlePageRequestClient(), 'ListRecords');
        $this->assertInstanceOf(ClientInterface::class, $iterator->getClient());
    }

    public function testGetResumptionTokenReturnsNullWhenNoTokenExists()
    {
        $iterator = new RecordIterator($this->singlePageRequestClient(), 'ListRecords');
        $iterator->next();
        $this->assertNull($iterator->getResumptionToken());
    }

    public function testGetResumptionTokenReturnsTokenWhenTokenExists()
    {
        $iterator = new RecordIterator($this->multiplePageRequestClient(), 'ListIdentifiers');
        $iterator->next();
        $this->assertNotNull($iterator->getResumptionToken());
    }

    public function testGetExpirationDateReturnsNullWhenNoDataExists()
    {
        $iterator = new RecordIterator($this->singlePageRequestClient(), 'ListRecords');
        $iterator->next();
        $this->assertNull($iterator->getExpirationDate());
    }

    public function testGetExpirationDateReturnsDateTimeWhenDataExists()
    {
        $iterator = new RecordIterator($this->multiplePageRequestClient(), 'ListIdentifiers');
        $iterator->next();
        $this->assertInstanceOf(DateTimeInterface::class, $iterator->getExpirationDate());
    }

    public function testKeyCallsNextItemWhenCurrItemIsNull()
    {
        $iterator = new RecordIterator($this->multiplePageRequestClient(), 'ListIdentifiers');
        $key = $iterator->key();
        $this->assertIsInt($key);
    }

    /**
     * @return Client
     * @throws Exception
     */
    protected function singlePageRequestClient(): Client
    {
        $handler = new MockHandler([
            new Response(
                200,
                ['Content-type' => 'text/xml'],
                fopen(__DIR__ . '/SampleXML/GoodResponseSinglePage.xml', 'r')
            )
        ]);

        return new Client('http://example.org', new GuzzleAdapter(new \GuzzleHttp\Client(['handler' => $handler])));
    }

    /**
     * @return Client
     * @throws Exception
     */
    protected function multiplePageRequestClient(): Client
    {
        $handler = new MockHandler([
            new Response(
                200,
                ['Content-type' => 'text/xml'],
                fopen(__DIR__ . '/SampleXML/GoodResponseFourPage_1.xml', 'r')
            )
        ]);

        return new Client('http://example.org', new GuzzleAdapter(new \GuzzleHttp\Client(['handler' => $handler])));
    }
}
