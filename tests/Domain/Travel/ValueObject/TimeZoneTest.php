<?php

namespace App\Tests\Domain\Travel\ValueObject;

use App\Domain\Travel\ValueObject\TimeZone;
use PHPUnit\Framework\TestCase;

class TimeZoneTest extends TestCase
{
    public function testValidIanaTimezoneIsAccepted(): void
    {
        $tz = new TimeZone('Europe/Madrid');

        $this->assertSame('Europe/Madrid', $tz->getValue());
        $this->assertSame('Europe/Madrid', (string) $tz);
    }

    public function testInvalidTimezoneThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Invalid IANA timezone/');

        new TimeZone('Not/ATimezone');
    }

    public function testToDateTimeZoneReturnsCorrectObject(): void
    {
        $tz = new TimeZone('Asia/Tokyo');

        $dtz = $tz->toDateTimeZone();

        $this->assertInstanceOf(\DateTimeZone::class, $dtz);
        $this->assertSame('Asia/Tokyo', $dtz->getName());
    }

    public function testFromStringCreatesInstance(): void
    {
        $tz = TimeZone::fromString('America/New_York');

        $this->assertSame('America/New_York', $tz->getValue());
    }

    public function testTryFromStringReturnsNullForInvalid(): void
    {
        $tz = TimeZone::tryFromString('Invalid/Zone');

        $this->assertNull($tz);
    }

    public function testTryFromStringReturnsInstanceForValid(): void
    {
        $tz = TimeZone::tryFromString('UTC');

        $this->assertNotNull($tz);
        $this->assertSame('UTC', $tz->getValue());
    }

    public function testToStringReturnsValue(): void
    {
        $tz = new TimeZone('Pacific/Auckland');

        $this->assertSame('Pacific/Auckland', (string) $tz);
    }
}
