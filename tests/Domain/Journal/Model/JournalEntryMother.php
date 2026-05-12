<?php

namespace App\Tests\Domain\Journal\Model;

use App\Domain\Journal\Model\JournalEntry;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;

class JournalEntryMother
{
    public static function create(?string $content = null): JournalEntry
    {
        return new JournalEntry(
            TravelMother::random(),
            UserMother::random(),
            new \DateTime('2024-06-15'),
            $content ?? 'A wonderful day exploring the city.'
        );
    }

    public static function forTravel(\App\Domain\Travel\Model\Travel $travel, \App\Domain\User\Model\User $author, \DateTime $date): JournalEntry
    {
        return new JournalEntry($travel, $author, $date, 'Journal entry content.');
    }

    public static function public(): JournalEntry
    {
        $entry = self::create();
        $entry->setIsPublic(true);

        return $entry;
    }

    public static function withMood(string $mood): JournalEntry
    {
        $entry = self::create();
        $entry->setMood($mood);

        return $entry;
    }

    public static function random(): JournalEntry
    {
        $entry = new JournalEntry(
            TravelMother::random(),
            UserMother::random(),
            new \DateTime(sprintf('2024-%02d-%02d', mt_rand(1, 12), mt_rand(1, 28))),
            uniqid('content_', true)
        );
        $entry->setTitle(uniqid('title_', true));

        return $entry;
    }
}
