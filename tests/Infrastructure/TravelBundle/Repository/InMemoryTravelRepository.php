<?php

namespace App\Tests\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Tests\Domain\Travel\Model\TravelMother;

class InMemoryTravelRepository implements TravelRepository
{
    public const TRAVEL_1 = 'Dummy1';
    public const TRAVEL_2 = 'Dummy2';
    public const TRAVEL_3 = 'Dummy3';
    public const TRAVEL_4 = 'Dummy4';

    private $travel = [];

    public function loadData(): void
    {
        $travel = TravelMother::withTitle(self::TRAVEL_1);
        $this->save($travel);
        $travel = TravelMother::withTitle(self::TRAVEL_2);
        $this->save($travel);
        $travel = TravelMother::withTitle(self::TRAVEL_3);
        $this->save($travel);
        $travel = TravelMother::withTitle(self::TRAVEL_4);
        $this->save($travel);
    }

    public function save(Travel $travel): void
    {
        /** @var User $user */
        $user = $travel->getUser();
        $travel->setSlug($travel->getTitle());

        //TODO Demeter remove
        $this->travel[] = [
            'travelId' => $travel->getId()->id(),
            'userId' => $user->getId()->id(),
            'travel' => $travel,
            'slug' => $travel->getSlug(),
        ];
    }

    public function ofIdOrFail(string $travelId)
    {
        return $this->getTravelById($travelId);
    }

    public function getTravelById(string $id): Travel
    {
        $travels = array_search($id, array_column($this->travel, 'travelId'));

        return $this->travel[$travels]['travel'];
    }

    public function findTravelBySlug(string $slug): ?Travel
    {
        $travels = $this->findByKeyValue('slug', $slug);

        return (sizeof($travels) > 0) ? $travels[0] : null;
    }

    private function findByKeyValue(string $key, string $value)
    {
        $result = [];

        $total = array_column($this->travel, $key);
        $users = array_keys($total, $value);
        foreach ($users as $user) {
            $result[] = $this->travel[$user]['travel'];
        }

        return $result;
    }

    public function ofSlugOrFail(string $slug)
    {
        return $this->findTravelBySlug($slug);
    }

    public function getAll(): array
    {
        return $this->travel;
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function findBy(array $criteria)
    {
        // TODO: Implement findBy() method.
    }
}
