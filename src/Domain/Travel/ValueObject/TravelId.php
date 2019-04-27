<?php

namespace App\Domain\Travel\ValueObject;

use App\Domain\Common\Model\IdentifiableDomainObject;
use Ramsey\Uuid\Uuid;

class TravelId extends IdentifiableDomainObject
{
    public function __construct($anId = null)
    {
        $this->id = $anId ?: Uuid::uuid4()->toString();
    }

    public function equalsTo(TravelId $travelId)
    {
        return $travelId->id === $this->id;
    }
}
