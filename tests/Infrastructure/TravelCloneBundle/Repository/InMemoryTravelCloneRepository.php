<?php

namespace App\Tests\Infrastructure\TravelCloneBundle\Repository;

use App\Domain\TravelClone\Model\TravelClone;
use App\Domain\TravelClone\Repository\TravelCloneRepository;

class InMemoryTravelCloneRepository implements TravelCloneRepository
{
    /** @var TravelClone[] */
    private $clones = [];

    public function save(TravelClone $travelClone): void
    {
        $this->clones[] = $travelClone;
    }

    public function findByOriginalTravelId(string $originalTravelId): array
    {
        return array_values(array_filter(
            $this->clones,
            function (TravelClone $tc) use ($originalTravelId) {
                return $tc->getOriginalTravelId() === $originalTravelId;
            }
        ));
    }

    public function findByClonedById(int $clonedById): array
    {
        return array_values(array_filter(
            $this->clones,
            function (TravelClone $tc) use ($clonedById) {
                return $tc->getClonedById() === $clonedById;
            }
        ));
    }

    public function findByClonedTravelId(string $clonedTravelId): ?TravelClone
    {
        foreach ($this->clones as $tc) {
            if ($tc->getClonedTravelId() === $clonedTravelId) {
                return $tc;
            }
        }

        return null;
    }

    public function getAll(): array
    {
        return $this->clones;
    }
}
