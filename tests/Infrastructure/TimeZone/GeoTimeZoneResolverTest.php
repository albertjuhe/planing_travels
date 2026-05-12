<?php

namespace App\Tests\Infrastructure\TimeZone;

use App\Infrastructure\TimeZone\GeoTimeZoneResolver;
use PHPUnit\Framework\TestCase;

class GeoTimeZoneResolverTest extends TestCase
{
    private GeoTimeZoneResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new GeoTimeZoneResolver();
    }

    public function testResolveMadridCoordinates(): void
    {
        $tz = $this->resolver->resolve(40.4168, -3.7038);

        $this->assertNotNull($tz);
        $this->assertStringContainsString('Europe', $tz);
    }

    public function testResolveTokyoCoordinates(): void
    {
        $tz = $this->resolver->resolve(35.6762, 139.6503);

        $this->assertNotNull($tz);
        $this->assertStringContainsString('Asia', $tz);
    }

    public function testResolveNewYorkCoordinates(): void
    {
        $tz = $this->resolver->resolve(40.7128, -74.0060);

        $this->assertNotNull($tz);
        $this->assertStringContainsString('America', $tz);
    }

    public function testResolveReturnsSomeValidIanaString(): void
    {
        $tz = $this->resolver->resolve(51.5074, -0.1278);

        $this->assertNotNull($tz);
        $this->assertContains($tz, \DateTimeZone::listIdentifiers());
    }
}
