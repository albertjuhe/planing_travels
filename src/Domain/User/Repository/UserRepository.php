<?php
namespace App\Domain\User\Repository;

use App\Domain\User\Model\User;

interface UserRepository
{
    public function save(User $user);
}