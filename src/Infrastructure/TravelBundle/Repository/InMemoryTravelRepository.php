<?php


namespace App\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;

class InMemoryTravelRepository implements TravelRepository
{
    private $travel = [];

    public function save(Travel $travel)
    {
        $this->travel[$travel->getId()] = $travel;
    }

    public function TravelsAllOrderedByStarts($maximResults)
    {
        // TODO: Implement TravelsAllOrderedByStarts() method.
    }

    public function getAllTravelsByUser(User $user)
    {
        // TODO: Implement getAllTravelsByUser() method.
    }

    public function findById(int $id): Travel
    {
        // TODO: Implement findById() method.
        return $this->travel[$id];
    }


}