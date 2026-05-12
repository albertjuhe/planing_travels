<?php

namespace App\Tests\Domain\Journal\Model;

use App\Domain\Journal\Model\JournalEntry;
use App\Domain\Journal\Model\JournalPhoto;
use PHPUnit\Framework\TestCase;

class JournalEntryTest extends TestCase
{
    public function testConstructorSetsDefaults(): void
    {
        $entry = JournalEntryMother::create('My first journal entry.');

        $this->assertNotEmpty($entry->getId());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $entry->getId()
        );
        $this->assertFalse($entry->isPublic());
        $this->assertNull($entry->getTitle());
        $this->assertNull($entry->getMood());
        $this->assertSame('My first journal entry.', $entry->getContent());
        $this->assertInstanceOf(\DateTime::class, $entry->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $entry->getUpdatedAt());
        $this->assertCount(0, $entry->getPhotos());
    }

    public function testSetTitleUpdatesUpdatedAt(): void
    {
        $entry = JournalEntryMother::create();
        $before = clone $entry->getUpdatedAt();
        usleep(1000);

        $entry->setTitle('Day in Paris');

        $this->assertSame('Day in Paris', $entry->getTitle());
        $this->assertGreaterThanOrEqual($before, $entry->getUpdatedAt());
    }

    public function testSetContentUpdatesContent(): void
    {
        $entry = JournalEntryMother::create('old content');

        $entry->setContent('new content');

        $this->assertSame('new content', $entry->getContent());
    }

    public function testSetValidMood(): void
    {
        $entry = JournalEntryMother::create();

        $entry->setMood(JournalEntry::MOOD_HAPPY);

        $this->assertSame(JournalEntry::MOOD_HAPPY, $entry->getMood());
        $this->assertSame('😊', $entry->getMoodEmoji());
    }

    public function testSetInvalidMoodThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $entry = JournalEntryMother::create();
        $entry->setMood('grumpy');
    }

    public function testSetMoodNullClearsIt(): void
    {
        $entry = JournalEntryMother::withMood(JournalEntry::MOOD_EXCITED);

        $entry->setMood(null);

        $this->assertNull($entry->getMood());
        $this->assertNull($entry->getMoodEmoji());
    }

    public function testSetIsPublicChangesVisibility(): void
    {
        $entry = JournalEntryMother::create();
        $this->assertFalse($entry->isPublic());

        $entry->setIsPublic(true);

        $this->assertTrue($entry->isPublic());
    }

    public function testAddPhotoAddsToCollection(): void
    {
        $entry = JournalEntryMother::create();
        $photo = new JournalPhoto($entry, 'photo.jpg');

        $entry->addPhoto($photo);

        $this->assertCount(1, $entry->getPhotos());
        $this->assertTrue($entry->getPhotos()->contains($photo));
    }

    public function testAddSamePhotoTwiceDoesNotDuplicate(): void
    {
        $entry = JournalEntryMother::create();
        $photo = new JournalPhoto($entry, 'photo.jpg');

        $entry->addPhoto($photo);
        $entry->addPhoto($photo);

        $this->assertCount(1, $entry->getPhotos());
    }

    public function testRemovePhotoRemovesFromCollection(): void
    {
        $entry = JournalEntryMother::create();
        $photo = new JournalPhoto($entry, 'photo.jpg');
        $entry->addPhoto($photo);

        $entry->removePhoto($photo);

        $this->assertCount(0, $entry->getPhotos());
    }

    public function testWeatherSnapshotRoundTrip(): void
    {
        $entry = JournalEntryMother::create();
        $weather = ['tempMin' => 15.5, 'tempMax' => 22.3, 'code' => 1, 'icon' => '☀️'];

        $entry->setWeatherSnapshot(json_encode($weather));

        $decoded = $entry->getWeatherSnapshotArray();
        $this->assertEqualsWithDelta(15.5, $decoded['tempMin'], 0.001);
        $this->assertEqualsWithDelta(22.3, $decoded['tempMax'], 0.001);
        $this->assertSame(1, $decoded['code']);
        $this->assertSame('☀️', $decoded['icon']);
    }

    public function testToArrayContainsRequiredKeys(): void
    {
        $entry = JournalEntryMother::random();

        $array = $entry->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('entryDate', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('content', $array);
        $this->assertArrayHasKey('mood', $array);
        $this->assertArrayHasKey('isPublic', $array);
        $this->assertArrayHasKey('photos', $array);
        $this->assertArrayHasKey('createdAt', $array);
    }
}
