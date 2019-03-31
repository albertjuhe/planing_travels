<?php

namespace App\Domain\Common\Model;

abstract class IdentifiableDomainObject
{
    protected $id;

    public function id()
    {
        return $this->id;
    }

    protected function setId($anId)
    {
        $this->id = $anId;
    }
}
