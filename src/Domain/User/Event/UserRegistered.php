<?php

namespace App\Domain\User\Event;

use App\Domain\Common\Event\DomainEvent;
use App\Domain\User\Model\UserId;

class UserRegistered implements DomainEvent
{
    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var string
     */
    private $userEmail;

    public function __construct(UserId $userId, string $userEmail)
    {
        $this->userId = $userId;
        $this->userEmail = $userEmail;
        $this->occurredOn = new \DateTime();
    }

    public function userId()
    {
        return $this->userId;
    }

    public function occurredOn()
    {
        return $this->occurredOn;
    }

    public function userEmail()
    {
        $this->userEmail;
    }
}
