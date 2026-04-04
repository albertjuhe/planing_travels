<?php

namespace App\Tests\Domain\User\Model;

use App\Domain\User\Model\User;

class UserMother
{
    public static function create()
    {
        return new User();
    }

    public static function withUserId(int $id): User
    {
        return self::create()::byId($id);
    }

    public static function random(): User
    {
        $user = self::create()::byId(mt_rand(1, 999999));
        $user->setUsername(uniqid('user_', true));

        return $user;
    }
}
