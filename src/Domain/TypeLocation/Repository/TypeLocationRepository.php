<?php
namespace App\Domain\TypeLocation\Repository;

interface TypeLocationRepository
{
    /**
     * Get all type of locations
     * @return mixed
     */
    public function getAllTypeLocations();
}