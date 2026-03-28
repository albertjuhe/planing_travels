<?php

namespace App\Tests\Application\UseCases\User;

use App\Infrastructure\UserBundle\Repository\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

class UserService extends TestCase
{
    protected $userRepository;

    public function setUp()
    {
        $this->userRepository = new InMemoryUserRepository();
    }
}
