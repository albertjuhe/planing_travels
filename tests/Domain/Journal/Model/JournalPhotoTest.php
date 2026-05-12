<?php

namespace App\Tests\Domain\Journal\Model;

use App\Domain\Journal\Model\JournalPhoto;
use PHPUnit\Framework\TestCase;

class JournalPhotoTest extends TestCase
{
    public function testConstructorSetsDefaults(): void
    {
        $entry = JournalEntryMother::create();
        $photo = new JournalPhoto($entry, 'photo.webp');

        $this->assertNotEmpty($photo->getId());
        $this->assertSame($entry, $photo->getEntry());
        $this->assertSame('photo.webp', $photo->getFilename());
        $this->assertNull($photo->getCaption());
        $this->assertNull($photo->getTakenAt());
        $this->assertNull($photo->getGeoLat());
        $this->assertNull($photo->getGeoLng());
        $this->assertNull($photo->getLinkedLocationId());
        $this->assertInstanceOf(\DateTime::class, $photo->getCreatedAt());
    }

    public function testSetCaption(): void
    {
        $photo = new JournalPhoto(JournalEntryMother::create(), 'img.jpg');

        $photo->setCaption('Sunset over the mountains');

        $this->assertSame('Sunset over the mountains', $photo->getCaption());
    }

    public function testSetTakenAt(): void
    {
        $photo = new JournalPhoto(JournalEntryMother::create(), 'img.jpg');
        $date = new \DateTime('2024-06-15 14:30:00');

        $photo->setTakenAt($date);

        $this->assertSame($date, $photo->getTakenAt());
    }

    public function testSetGeoCoordinates(): void
    {
        $photo = new JournalPhoto(JournalEntryMother::create(), 'img.jpg');

        $photo->setGeoLat(40.4168);
        $photo->setGeoLng(-3.7038);

        $this->assertSame(40.4168, $photo->getGeoLat());
        $this->assertSame(-3.7038, $photo->getGeoLng());
    }

    public function testSetLinkedLocationId(): void
    {
        $photo = new JournalPhoto(JournalEntryMother::create(), 'img.jpg');

        $photo->setLinkedLocationId('loc-uuid-123');

        $this->assertSame('loc-uuid-123', $photo->getLinkedLocationId());
    }

    public function testToArrayContainsRequiredKeys(): void
    {
        $photo = new JournalPhoto(JournalEntryMother::create(), 'photo.jpg');
        $photo->setCaption('A caption');
        $photo->setGeoLat(48.8);
        $photo->setGeoLng(2.3);

        $array = $photo->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('filename', $array);
        $this->assertArrayHasKey('caption', $array);
        $this->assertArrayHasKey('takenAt', $array);
        $this->assertArrayHasKey('geoLat', $array);
        $this->assertArrayHasKey('geoLng', $array);
        $this->assertArrayHasKey('linkedLocationId', $array);
        $this->assertSame('photo.jpg', $array['filename']);
        $this->assertSame('A caption', $array['caption']);
        $this->assertSame(48.8, $array['geoLat']);
    }

    public function testTwoPhotosHaveDifferentIds(): void
    {
        $entry = JournalEntryMother::create();
        $p1 = new JournalPhoto($entry, 'a.jpg');
        $p2 = new JournalPhoto($entry, 'b.jpg');

        $this->assertNotSame($p1->getId(), $p2->getId());
    }
}
