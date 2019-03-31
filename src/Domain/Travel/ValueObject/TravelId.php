<?php

namespace App\Domain\Travel\ValueObject;

use Ramsey\Uuid\Uuid;

class TravelId
{
    private $id;

    public function __construct($anId = null)
    {
        $this->id = $anId ?: Uuid::uuid4()->toString();
    }

    public static function create($anId = null)
    {
        return new static($anId);
    }

    public function __toString()
    {
        return $this->id;
    }

    public function id()
    {
        return $this->id;
    }

    public function equalsTo(TravelId $travelId)
    {
        return $travelId->id === $this->id;
    }
}
