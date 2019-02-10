<?php

namespace App\Domain\Travel\Repository;

use App\Domain\User\Model\User;
use App\Domain\Travel\Model\Travel;

interface TravelRepository
{
    /**
     * @param int $travelId
     * @return Travel
     */
    public function ofIdOrFail(int $travelId): Travel;

    /**
     * @param string $slug
     * @return mixed
     */
    public function ofSlugOrFail(string $slug);

    /**
     * Return the travels with max starts
     * @param $maximResults
     * @return mixed
     */
    public function TravelsAllOrderedBy($maximResults);

    /**
     * @param $user
     * @return mixed
     */
    public function getAllTravelsByUser(User $user);

    /**
     * @param Travel $travel
     * @return mixed
     */
    public function save(Travel $travel);

    /**
     * @param Integer $id
     * @return mixed
     */
    public function getTravelById(int $id): Travel;

    /**
     * @param array $criteria
     * @return mixed
     */
    public function findBy(array $criteria);

    /**
     * Find by Id
     * @param $id
     * @return mixed
     */
    public function find($id);

}