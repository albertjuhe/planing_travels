<?php


namespace App\Infrastructure\UserBundle\Repository;

use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Model\User;

class InMemoryUserRepository implements UserRepository
{
    private $users = [];

    public function UserByUsername(string $username): ?User
    {
        if (isset($this->users[$username])) {
            return $this->users[$username];
        } return null;
    }

    public function save(User $user)
    {
        $this->users[$user->getUsername()] = $user;
    }
}