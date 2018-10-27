<?php


namespace App\Domain\User\Model;

class UserId
{
    private $id;

    public function __construct($anId)
    {
        $this->id = $anId;
    }
    public function id()
    {
        return $this->id;
    }
    public function equalsTo(UserId $anUserId)
    {
        return $anUserId->id === $this->id;
    }
}