<?php
/**
 * Created by PhpStorm.
 * User: ajuhe
 * Date: 8/07/18
 * Time: 21:57
 */

namespace App\Domain\Travel\Repository;


interface TravelRepository
{
    /**
     * Return the travels with max starts
     * @param $maximResults
     * @return mixed
     */
    public function findAllOrderedByStarts($maximResults);
}