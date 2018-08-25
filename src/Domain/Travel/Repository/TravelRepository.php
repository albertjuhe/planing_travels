<?php
/**
 * Created by PhpStorm.
 * User: ajuhe
 * Date: 8/07/18
 * Time: 21:57
 */

namespace App\Domain\Travel\Repository;

use App\Domain\User\Model\User;
use App\Domain\Travel\Model\Travel;

interface TravelRepository
{
    /**
     * Return the travels with max starts
     * @param $maximResults
     * @return mixed
     */
    public function TravelsAllOrderedByStarts($maximResults);

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
}