<?php

namespace App\Domain\Location\ValueObject;

use App\Domain\Common\Model\IdentifiableDomainObject;
use Ramsey\Uuid\Uuid;

class LocationId extends IdentifiableDomainObject
{
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

    public function equalsTo(LocationId $locationId)
    {
        return $locationId->id === $this->id;
    }
}
