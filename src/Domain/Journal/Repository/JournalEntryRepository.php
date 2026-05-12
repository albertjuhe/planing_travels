<?php

namespace App\Domain\Journal\Repository;

use App\Domain\Journal\Model\JournalEntry;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;

interface JournalEntryRepository
{
    public function save(JournalEntry $entry): void;

    public function remove(JournalEntry $entry): void;

    public function findById(string $id): ?JournalEntry;

    /** @return JournalEntry[] */
    public function findByTravel(Travel $travel, bool $publicOnly = false): array;

    /** @return JournalEntry[] grouped by date string */
    public function findByTravelGroupedByDate(Travel $travel, bool $publicOnly = false): array;
}
