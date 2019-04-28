<?php

namespace App\Application\Query\Travel;

use App\Application\Query\Query;

class BestTravelsListQuery implements Query
{
    private $numberMaxOfTravels;
    private $orderedBy;

      public function __construct(
          int $numberMaxOfTravels,
          string $orderedBy
      )
    {
        $this->numberMaxOfTravels = $numberMaxOfTravels;
        $this->orderedBy = $orderedBy;
    }

    public function getNumberMaxOfTravels(): int
    {
        return $this->numberMaxOfTravels;
    }
  public function getOrderedBy(): string
    {
        return $this->orderedBy;
    }
}
