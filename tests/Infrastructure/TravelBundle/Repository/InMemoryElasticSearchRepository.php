<?php

namespace App\Tests\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\IndexerRepository;
use App\Domain\Travel\Repository\TravelReadModelRepository;

class InMemoryElasticSearchRepository implements IndexerRepository, TravelReadModelRepository
{
    private array $documents = [];

    public function save(Travel $travel): void
    {
        $this->documents[$travel->getId()->id()] = [
            'id'     => $travel->getId()->id(),
            'title'  => $travel->getTitle(),
            'stars'  => $travel->getStars(),
            'watch'  => $travel->getWatch(),
            'status' => $travel->getStatus(),
        ];
    }

    public function refresh(): void
    {
    }

    public function getTravelOrderedBy(string $order, int $maxResults): array
    {
        $published = array_filter(
            $this->documents,
            fn($doc) => $doc['status'] === Travel::TRAVEL_PUBLISHED
        );

        usort($published, fn($a, $b) => $b[$order] <=> $a[$order]);

        return array_slice(array_values($published), 0, $maxResults);
    }

    public function getAllTravelsByUser(int $user): array
    {
        return [];
    }

    public function getDocuments(): array
    {
        return $this->documents;
    }
}
