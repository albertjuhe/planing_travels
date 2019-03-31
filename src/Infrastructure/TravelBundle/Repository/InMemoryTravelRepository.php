<?php

namespace App\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Domain\Travel\ValueObject\GeoLocation;

class InMemoryTravelRepository implements TravelRepository
{
    private $travel = [];

    public function loadData()
    {
        $travel = Travel::fromTitleAndGeolocationAndUser('Dummy1',
            new GeoLocation(1, 2, 3, 4, 5, 6),
            User::byId(1));
        $travel->setId(1);
        $this->save($travel);

        $travel = Travel::fromTitleAndGeolocationAndUser('Dummy2',
            new GeoLocation(7, 8, 9, 10, 11, 12),
            User::byId(2));
        $travel->setId(2);
        $travel->setStarts(5);
        $this->save($travel);

        $travel = Travel::fromTitleAndGeolocationAndUser('Dummy3',
            new GeoLocation(13, 21, 31, 41, 51, 61),
            User::byId(1));
        $travel->setId(3);
        $travel->setStarts(25);
        $this->save($travel);

        $travel = Travel::fromTitleAndGeolocationAndUser('Dummy4',
            new GeoLocation(12, 22, 32, 42, 52, 62),
            User::byId(1));
        $travel->setId(4);
        $travel->setStarts(91);
        $this->save($travel);
    }

    public function save(Travel $travel)
    {
        //TODO Demeter remove
        $this->travel[] = [
            'travelId' => $travel->getId(),
            'userId' => $travel->getUser()->userId(),
            'travel' => $travel,
            'slug' => $travel->getSlug(),
        ];
    }

    public function TravelsAllOrderedBy($maximResults)
    {
        // TODO: Implement TravelsAllOrderedByStarts() method.
    }

    public function ofIdOrFail(string $travelId): Travel
    {
        return array_search($travelId, array_column($this->travel, 'travelId'));
    }

    /**
     * @param User $user
     *
     * @return array|mixed
     */
    public function getAllTravelsByUser(User $user)
    {
        return $this->findByKeyValue('userId', $user->getUserId());
    }

    public function getTravelById(int $id): Travel
    {
        $travels = array_search($id, array_column($this->travel, 'travelId'));

        return $this->travel[$travels]['travel'];
    }

    public function findTravelBySlug(string $slug): Travel
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

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function findBy(array $criteria)
    {
        // TODO: Implement findBy() method.
    }
}
