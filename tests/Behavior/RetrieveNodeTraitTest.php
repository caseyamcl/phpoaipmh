<?php
declare(strict_types=1);

namespace Phpoaipmh\Behavior;

use DateTimeImmutable;
use DOMDocument;
use Phpoaipmh\Exception\MalformedResponseException;
use PHPUnit\Framework\TestCase;

class RetrieveNodeTraitTest extends TestCase
{
    private const GOOD_XML_FILEPATH = __DIR__ . '/../Fixture/SampleXML/GoodResponseIdentify.xml';

    use RetrieveNodeTrait;

    public function testRetrieveNodeValueBasicFunctionality(): void
    {
        $doc = $this->loadXML();
        $this->assertSame('http://nsdl.org/oai', self::retrieveNodeValue($doc, 'baseURL'));
    }

    public function testRetrieveNonExistentValue(): void
    {
        $this->expectException(MalformedResponseException::class);
        $this->expectExceptionMessage('Expected');

        $doc = $this->loadXML();
        self::retrieveNodeValue($doc, 'nonExistentValue');
    }

    public function testRetrieveNodeWithMultipleValuesGeneratesNotice(): void
    {
        $this->expectNotice();
        $this->expectNoticeMessage('contains more than a single element');

        $doc = $this->loadXML();
        self::retrieveNodeValue($doc, 'description');
    }

    public function testRetrieveNodeWithMultipleValuesReturnsExpectedResult(): void
    {
        $doc = $this->loadXML();
        $retrieved = self::retrieveNodeValues($doc, 'adminEmail');
        $this->assertTrue(count($retrieved) === 2);
        $this->assertSame(['jweather@example.org', 'joe@example.org'], $retrieved);
    }

    public function testRetrieveNodeWithMultipleValuesReturnsEmptyArrayIfNonExistentAndOptional(): void
    {
        $doc = $this->loadXML();
        $this->assertSame([], self::retrieveNodeValues($doc, 'nonExistent', false));
    }

    public function testRetrieveNodeWithMultipleValuesThrowsExceptionForNonExistentValue(): void
    {
        $this->expectException(MalformedResponseException::class);
        $this->expectExceptionMessage('Expected');

        $doc = $this->loadXML();
        self::retrieveNodeValues($doc, 'nonExistentValue');
    }

    public function testRetrieveNodeDateValueReturnsDateTimeObject(): void
    {
        $doc = $this->loadXML();
        $this->assertInstanceOf(
            DateTimeImmutable::class,
            self::retrieveNodeDateValue($doc, 'earliestDatestamp')
        );
    }

    public function testRetrieveNodeDateValueThrowsExceptionForNonExistentValue(): void
    {
        $this->expectException(MalformedResponseException::class);
        $this->expectExceptionMessage('Expected');

        $doc = $this->loadXML();
        self::retrieveNodeDateValue($doc, 'nonExistent');
    }

    public function testRetrieveDateValueThrowsMalformedResponseExceptionOnDateParseError(): void
    {
        $this->expectException(MalformedResponseException::class);
        $this->expectExceptionMessage('invalid date value');

        $doc = new DOMDocument();
        $doc->loadXML('<earliestDatestamp>_</earliestDatestamp>');
        self::retrieveNodeDateValue($doc, 'earliestDatestamp');
    }

    protected function loadXML(): DOMDocument
    {
        $doc = new DOMDocument();
        $doc->loadXML(file_get_contents(static::GOOD_XML_FILEPATH));
        return $doc;
    }

    protected static function getXMLDocumentName(): string
    {
        return 'Test';
    }
}
