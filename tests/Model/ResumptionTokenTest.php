<?php

/**
 * PHPOAIPMH Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/phpoaipmh
 * @Version 4.0
 * @package caseyamcl/phpoaipmh
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, -please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace Phpoaipmh\Model;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

class ResumptionTokenTest extends TestCase
{
    public function testNewInstance(): void
    {
        $rt = new ResumptionToken('abc123');
        $this->assertInstanceOf(ResumptionToken::class, $rt);
    }

    public function testGetExpirationDateReturnsDateTimeObjectWhenExists(): void
    {
        $rt = new ResumptionToken(
            'abc123',
            20,
            5,
            DateTime::createFromFormat('Y-m-d', '2018-01-05')
        );

        $this->assertInstanceOf(DateTimeInterface::class, $rt->getExpirationDate());
        $this->assertSame('2018-01-05', $rt->getExpirationDate()->format('Y-m-d'));
    }

    public function testGetExpirationReturnsNullWhenNotSet(): void
    {
        $rt = new ResumptionToken('abc123', 20, 5);
        $this->assertNull($rt->getExpirationDate());
    }

    public function testIsValidReturnsTrueWhenExpirationDateHasNotPassed(): void
    {
        $rt = new ResumptionToken(
            'abc123',
            20,
            5,
            (new DateTimeImmutable())->add(new DateInterval('P1D'))
        );

        $this->assertTrue($rt->isValid());
    }

    public function testIsValidReturnsFalseWhenExpirationDateHasPassed(): void
    {
        $rt = new ResumptionToken(
            'abc123',
            20,
            5,
            (new DateTimeImmutable())->sub(new DateInterval('P1D'))
        );

        $this->assertFalse($rt->isValid());
    }

    public function testFromString(): void
    {
        $xml = '<resumptionToken completeListSize="733" cursor="4" expirationDate="2099-01-01T01:30:28Z">
                0/200/733/nsdl_dc/null/2012-07-26/null
                </resumptionToken>';

        $rt = ResumptionToken::fromString($xml);
        $this->assertSame(' 0/200/733/nsdl_dc/null/2012-07-26/null', $rt->getToken());
        $this->assertSame(733, $rt->getCompleteListSize());
        $this->assertSame(4, $rt->getCursor());
        $this->assertEquals('2099-01-01T01:30:28Z', $rt->getExpirationDate()->format('c'));
    }

    public function testGetTokenReturnsStringToken(): void
    {
        $rt = new ResumptionToken('abc123');
        $this->assertIsString($rt->getToken());
        $this->assertSame('abc123', $rt->getToken());
    }

    public function testToString(): void
    {
        $rt = new ResumptionToken('abc123');
        $this->assertSame('abc123', (string) $rt->getToken());
    }

    public function testGetCursorReturnsIntegerWhenExists(): void
    {
        $rt = new ResumptionToken('abc123', 100, 20);
        $this->assertIsInt($rt->getCursor());
        $this->assertSame(20, $rt->getCursor());
    }

    public function testGetCursorReturnsNullWhenNotExists(): void
    {
        $rt = new ResumptionToken('abc123', 100);
        $this->assertNull($rt->getCursor());
    }

    public function testHasExpirationDate(): void
    {
        $rt1 = new ResumptionToken('abc123', 100, 20, new DateTime());
        $this->assertTrue($rt1->hasExpirationDate());

        $rt2 = new ResumptionToken('abc123', 100, 20);
        $this->assertFalse($rt2->hasExpirationDate());
    }

    public function testHasCursor(): void
    {
        $rt1 = new ResumptionToken('abc123', 100, 20);
        $this->assertTrue($rt1->hasCursor());

        $rt2 = new ResumptionToken('abc123', 100);
        $this->assertFalse($rt2->hasCursor());
    }

    public function testHasCompleteListSize(): void
    {
        $rt1 = new ResumptionToken('abc123', 100);
        $this->assertTrue($rt1->hasCompleteListSize());

        $rt2 = new ResumptionToken('abc123');
        $this->assertFalse($rt2->hasCompleteListSize());
    }

    public function testGetCompleteListSizeReturnsIntegerWhenExists(): void
    {
        $rt = new ResumptionToken('abc123', 100);
        $this->assertIsInt($rt->getCompleteListSize());
        $this->assertSame(100, $rt->getCompleteListSize());
    }

    public function testGetCompleteListSizeReturnsNullWhenNotExists(): void
    {
        $rt = new ResumptionToken('abc123');
        $this->assertNull($rt->getCompleteListSize());
    }
}
