<?php

namespace App\Tests\Infrastructure\UserBundle\Repository;

use App\Domain\User\Model\User;
use App\Infrastructure\UserBundle\Repository\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

class UpdateProfileRepositoryTest extends TestCase
{
    /** @var InMemoryUserRepository */
    private $userRepository;

    public function setUp(): void
    {
        $this->userRepository = new InMemoryUserRepository();
    }

    public function testSaveUpdatedProfile(): void
    {
        $user = User::fromId(1);
        $user->setUsername('johndoe');
        $user->setEmail('john@example.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');

        $this->userRepository->save($user);

        $found = $this->userRepository->UserByUsername('johndoe');
        $this->assertNotNull($found);
        $this->assertEquals('john@example.com', $found->getEmail());
        $this->assertEquals('John', $found->getFirstName());
        $this->assertEquals('Doe', $found->getLastName());
    }

    public function testSaveOverwritesExistingUser(): void
    {
        $user = User::fromId(1);
        $user->setUsername('johndoe');
        $user->setEmail('old@example.com');
        $this->userRepository->save($user);

        $user->setEmail('new@example.com');
        $this->userRepository->save($user);

        $found = $this->userRepository->UserByUsername('johndoe');
        $this->assertEquals('new@example.com', $found->getEmail());
    }

    public function testUserByUsernameReturnsNullWhenNotFound(): void
    {
        $result = $this->userRepository->UserByUsername('nonexistent');
        $this->assertNull($result);
    }

    public function testSaveUpdatedPassword(): void
    {
        $user = User::fromId(1);
        $user->setUsername('johndoe');
        $user->setPassword('old_hashed_password');
        $this->userRepository->save($user);

        $user->setPassword('new_hashed_password');
        $this->userRepository->save($user);

        $found = $this->userRepository->UserByUsername('johndoe');
        $this->assertEquals('new_hashed_password', $found->getPassword());
    }
}
