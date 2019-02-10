<?php
namespace App\Domain\User\Repository;

use App\Domain\User\Model\User;

interface UserRepository
{
    public function UserByUsername(string $username): ?User;

    public function save(User $user);

    public function ofIdOrFail(int $userId): ?User;
}