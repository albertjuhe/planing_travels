<?php

namespace App\Domain\Common\Model;

abstract class IdentifiableDomainObject
{
    protected $id;

    public function id()
    {
        return $this->id;
    }

    /**
     * @return static
     */
    public static function create($anId = null)
    {
        return new static($anId);
    }

    public function __toString()
    {
        return $this->id;
    }

    protected function setId($anId)
    {
        $this->id = $anId;
    }
}
