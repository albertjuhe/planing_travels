<?php

namespace App\Tests\Repository;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\UserBundle\Repository\InMemoryUserRepository;
use App\Domain\User\Model\User;

class InMemoryUserRepositoryTest extends TestCase
{
    /** @var InMemoryUserRepository */
    private $inMemoryUserRepository;

    public function setUp()
    {
        $this->inMemoryUserRepository = new InMemoryUserRepository();
    }

    public function testOfIdOrFail()
    {
        /** @var User $user */
        $user = User::byId(1);
        $userNew = $this->inMemoryUserRepository->ofIdOrFail(1);
        $this->assertEquals($user->userId(), $userNew->userId());
    }

    public function testSave()
    {
        $user = User::byId(1);
        $user->setUsername('newusername');
        $this->inMemoryUserRepository->save($user);
        $newUser = $this->inMemoryUserRepository->UserByUsername('newusername');
        $this->assertEquals($user->userId(), $newUser->userId());
    }
}
