<?php

namespace App\Domain\User\ValueObject;

use App\Domain\Common\Model\IdentifiableDomainObject;

class UserId extends IdentifiableDomainObject
{
    public function __construct($anId)
    {
        $this->id = $anId;
    }

    public function equalsTo(UserId $anUserId)
    {
        return $anUserId->id === $this->id;
    }

    public function __toString()
    {
        return (string) $this->id();
    }
}
