<?php


namespace App\Domain\Travel\Repository;


use App\Domain\Travel\Model\Travel;

interface IndexerRepository
{

    public function save(Travel $travel);

    public function refresh();
}