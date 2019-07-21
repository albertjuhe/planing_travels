<?php

namespace App\Domain\Travel\Repository;

use App\Domain\Travel\Model\Travel;

interface TravelRepository
{
    /**
     * @param int $travelId
     *
     * @return Travel
     */
    public function ofIdOrFail(string $travelId): Travel;

    /**
     * @param string $slug
     *
     * @return mixed
     */
    public function ofSlugOrFail(string $slug);

    /**
     * @param Travel $travel
     *
     * @return mixed
     */
    public function save(Travel $travel);

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function getTravelById(string $id): Travel;

    /**
     * @param array $criteria
     *
     * @return mixed
     */
    public function findBy(array $criteria);

    /**
     * Find by Id.
     *
     * @param $id
     *
     * @return mixed
     */
    public function find($id);

    public function getAll();
}
