<?php

namespace App\Tests\Infrastructure\Repository;

use App\Domain\User\Exceptions\UserDoesntExists;
use App\Domain\User\ValueObject\UserId;
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
        $userId = mt_rand();
        /** @var User $user */
        $user = User::byId($userId);
        $userNew = $this->inMemoryUserRepository->ofIdOrFail(new UserId($userId));
        $this->assertEquals($user->userId(), $userNew->userId());
    }

    public function testOfFailUser()
    {
        $userId = 0;
        $this->expectException(UserDoesntExists::class);
        $this->inMemoryUserRepository->ofIdOrFail(new UserId($userId));
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
