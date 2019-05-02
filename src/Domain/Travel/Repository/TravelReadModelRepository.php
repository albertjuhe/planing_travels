<?php

namespace App\Domain\Travel\Repository;

use App\Domain\User\Model\User;

interface TravelReadModelRepository
{
    /**
     * Return the travels with max starts.
     *
     * @param $maximResults
     *
     * @return mixed
     */
    public function getTravelOrderedBy(string $order, int $maximResults);

    /**
     * @param $user
     *
     * @return mixed
     */
    public function getAllTravelsByUser(int $user);
}
