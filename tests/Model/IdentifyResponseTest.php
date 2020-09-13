<?php
declare(strict_types=1);

namespace Phpoaipmh\Model;

use DateTime;
use DateTimeImmutable;
use DOMNode;
use InvalidArgumentException;
use Phpoaipmh\Granularity;
use PHPUnit\Framework\TestCase;
use stdClass;

class IdentifyResponseTest extends TestCase
{
    private const GOOD_XML_FILEPATH = __DIR__ . '/../Fixture/SampleXML/GoodResponseIdentify.xml';

    public function testInstantiateFromXmlString()
    {
        $pageObj = IdentifyResponse::fromXmlString(file_get_contents(self::GOOD_XML_FILEPATH));
        $this->assertInstanceOf(IdentifyResponse::class, $pageObj);
    }

    public function testInstantiateViaConstructorWithValidValues(): void
    {
        $obj = new IdentifyResponse(
            'Test Identify',
            'http://example.org/oai',
            '2.0',
            new DateTimeImmutable(),
            IdentifyResponse::DELETED_RECORD_TRANSIENT,
            new Granularity(Granularity::DATE_AND_TIME),
            ['test@example.org'],
            'gzip',
            [new DOMNode(), new DOMNode()]
        );

        $this->assertInstanceOf(IdentifyResponse::class, $obj);
    }

    public function testInstantiateWithInvalidBaseURLValueThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value for "baseURL"');

        new IdentifyResponse(
            'Test Identify',
            '@@@', // <-- Invalid URL
            '2.0',
            new DateTimeImmutable(),
            IdentifyResponse::DELETED_RECORD_TRANSIENT,
            new Granularity(Granularity::DATE_AND_TIME),
            ['test@example.org'],
            'gzip',
            []
        );
    }

    public function testInstantiateWithInvalidVersionGeneratesNotice(): void
    {
        $this->expectNotice();
        $this->expectNoticeMessage('library is designed to work with OAI-PMH 2.0');

        new IdentifyResponse(
            'Test Identify',
            'http://example.org/oai',
            '1.0', // <-- Non 2.0 Version
            new DateTimeImmutable(),
            IdentifyResponse::DELETED_RECORD_TRANSIENT,
            new Granularity(Granularity::DATE_AND_TIME),
            ['test@example.org'],
            'gzip',
            []
        );
    }

    public function testInstantiateWithNonImmutableDateTimeObjectConvertsToImmutable()
    {
        $obj = new IdentifyResponse(
            'Test Identify',
            'http://example.org/oai',
            '2.0',
            new DateTime('2010-09-08'), // <-- Mutable date/time
            IdentifyResponse::DELETED_RECORD_TRANSIENT,
            new Granularity(Granularity::DATE_AND_TIME),
            ['test@example.org'],
            'gzip',
            []
        );

        $this->assertInstanceOf(DateTimeImmutable::class, $obj->getEarliestDatestamp());
        $this->assertSame('2010-09-08', $obj->getEarliestDatestamp()->format('Y-m-d'));
    }

    public function testInstantiateWithInvalidPolicyThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid deleted record policy value');

        new IdentifyResponse(
            'Test Identify',
            'http://example.org/oai',
            '2.0',
            new DateTimeImmutable(),
            'blarh', // <-- Invalid deleted record policy
            new Granularity(Granularity::DATE_AND_TIME),
            ['test@example.org'],
            'gzip',
            []
        );
    }

    public function testInstantiateWithInvalidAdminEmailThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email at index 1');

        new IdentifyResponse(
            'Test Identify',
            'http://example.org/oai',
            '2.0',
            new DateTimeImmutable(),
            IdentifyResponse::DELETED_RECORD_TRANSIENT,
            new Granularity(Granularity::DATE_AND_TIME),
            ['test@example.org', 'test...x@example.org'], // <-- Invalid email at index 1
            'gzip',
            []
        );
    }

    public function testInstantiateWithInvalidDescriptionsThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid description at index 1');

        new IdentifyResponse(
            'Test Identify',
            'http://example.org/oai',
            '2.0',
            new DateTimeImmutable(),
            IdentifyResponse::DELETED_RECORD_TRANSIENT,
            new Granularity(Granularity::DATE_AND_TIME),
            ['test@example.org'],
            'gzip',
            [new DOMNode(), new stdClass(), new DOMNode()] // <-- Non DOMNode object at index 1
        );
    }

    public function testGetDescriptionCountWhenDescriptionExists()
    {
        $obj = new IdentifyResponse(
            'Test Identify',
            'http://example.org/oai',
            '2.0',
            new DateTimeImmutable(),
            IdentifyResponse::DELETED_RECORD_TRANSIENT,
            new Granularity(Granularity::DATE_AND_TIME),
            ['test@example.org'],
            'gzip',
            [new DOMNode(), new DOMNode(), new DOMNode()]
        );

        $this->assertSame(3, $obj->getDescriptionCount());
    }

    public function testGetDescriptionCountWhenDescriptionNotExists()
    {
        $obj = new IdentifyResponse(
            'Test Identify',
            'http://example.org/oai',
            '2.0',
            new DateTimeImmutable(),
            IdentifyResponse::DELETED_RECORD_TRANSIENT,
            new Granularity(Granularity::DATE_AND_TIME),
            ['test@example.org'],
            'gzip',
            []
        );

        $this->assertSame(0, $obj->getDescriptionCount());
    }

    public function testHasCompressionReturnsTrueWhenValuePresent(): void
    {
        $obj = new IdentifyResponse(
            'Test Identify',
            'http://example.org/oai',
            '2.0',
            new DateTimeImmutable(),
            IdentifyResponse::DELETED_RECORD_TRANSIENT,
            new Granularity(Granularity::DATE_AND_TIME),
            ['test@example.org'],
            'gzip',
            []
        );

        $this->assertTrue($obj->hasCompression());
        $this->assertSame('gzip', $obj->getCompression());
    }

    public function testHasCompressionReturnsFalseWhenValueNotPresent(): void
    {
        $obj = new IdentifyResponse(
            'Test Identify',
            'http://example.org/oai',
            '2.0',
            new DateTimeImmutable(),
            IdentifyResponse::DELETED_RECORD_TRANSIENT,
            new Granularity(Granularity::DATE_AND_TIME),
            ['test@example.org'],
            '', // <-- Notice that compression is an empty string, not NULL
            []
        );

        $this->assertFalse($obj->hasCompression());
        $this->assertNull($obj->getCompression());
    }

    public function testGetters()
    {
        $obj = new IdentifyResponse(
            'Test Identify',
            'http://example.org/oai',
            '2.0',
            new DateTimeImmutable(),
            IdentifyResponse::DELETED_RECORD_TRANSIENT,
            new Granularity(Granularity::DATE_AND_TIME),
            ['test@example.org', 'bob@example.org'],
            'gzip',
            [new DOMNode(), new DOMNode()]
        );

        $this->assertSame('Test Identify', $obj->getRepositoryName());
        $this->assertSame('http://example.org/oai', $obj->getBaseURL());
        $this->assertSame(IdentifyResponse::DELETED_RECORD_TRANSIENT, $obj->getDeletedRecordPolicy());
        $this->assertInstanceOf(Granularity::class, $obj->getGranularity());
        $this->assertSame('test@example.org', $obj->getFirstAdminEmail());
        $this->assertSame(['test@example.org', 'bob@example.org'], $obj->getAdminEmails());
        $this->assertSame('2.0', $obj->getProtocolVersion());
        $this->assertSame(2, count($obj->getDescriptions()));
        $this->assertInstanceOf(DateTimeImmutable::class, $obj->getEarliestDatestamp());
    }

    public function testToStringReturnsXML()
    {
        new IdentifyResponse(
            'Test Identify',
            'http://example.org/oai',
            '2.0',
            new DateTimeImmutable(),
            IdentifyResponse::DELETED_RECORD_TRANSIENT,
            new Granularity(Granularity::DATE_AND_TIME),
            ['test@example.org', 'bob@example.org'],
            'gzip',
            [new DOMNode(), new DOMNode()]
        );

        $this->assertSame(1, 1);
        // TODO: Write logic for and test __toString()
    }
}
