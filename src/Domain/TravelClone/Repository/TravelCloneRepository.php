<?php

namespace App\Domain\TravelClone\Repository;

use App\Domain\TravelClone\Model\TravelClone;

interface TravelCloneRepository
{
    public function save(TravelClone $travelClone): void;

    public function findByOriginalTravelId(string $originalTravelId): array;

    public function findByClonedById(int $clonedById): array;

    public function findByClonedTravelId(string $clonedTravelId): ?TravelClone;
}
