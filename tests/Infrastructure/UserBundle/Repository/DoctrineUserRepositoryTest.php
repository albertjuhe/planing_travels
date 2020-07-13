<?php

namespace App\Tests\Infrastructure\UserBundle\Repository;

use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;

class DoctrineUserRepositoryTest extends TestCase
{
    public function setUp()
    {
    }

    public function saveTest()
    {
        $userId = mt_rand();
        $user = User::byId($userId);
    }
}
