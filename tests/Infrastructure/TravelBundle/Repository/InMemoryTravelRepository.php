<?php

namespace App\Tests\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Tests\Domain\Travel\ValueObject\GeoLocationStub;
use App\Tests\Domain\User\Model\UserMother;

class InMemoryTravelRepository implements TravelRepository
{
    const TRAVEL_1 = 'Dummy1';
    const TRAVEL_2 = 'Dummy2';
    const TRAVEL_3 = 'Dummy3';
    const TRAVEL_4 = 'Dummy4';

    private $travel = [];

    public function loadData(): void
    {
        $travel = Travel::fromTitleAndGeolocationAndUser(
            self::TRAVEL_1,
            GeoLocationStub::random(),
            UserMother::random()
        );
        $this->save($travel);

        $travel = Travel::fromTitleAndGeolocationAndUser(
            self::TRAVEL_2,
            GeoLocationStub::random(),
            UserMother::random()
        );

        $travel->setStars(5);
        $this->save($travel);

        $travel = Travel::fromTitleAndGeolocationAndUser(
            self::TRAVEL_3,
            GeoLocationStub::random(),
            UserMother::random()
        );
        $travel->setStars(25);
        $this->save($travel);

        $travel = Travel::fromTitleAndGeolocationAndUser(
            self::TRAVEL_4,
            GeoLocationStub::random(),
            UserMother::random()
        );
        $travel->setStars(91);
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
        return array_search($travelId, array_column($this->travel, 'travelId'));
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
