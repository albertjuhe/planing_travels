<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Model\User;
use App\Domain\User\ValueObject\UserId;

interface UserRepository
{
    public function UserByUsername(string $username): ?User;

    public function save(User $user);

    public function ofIdOrFail(UserId $userId): ?User;
}
