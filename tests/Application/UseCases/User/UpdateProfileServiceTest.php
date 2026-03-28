<?php

namespace App\Tests\Application\UseCases\User;

use App\Domain\User\Model\User;

class UpdateProfileServiceTest extends UserService
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testUpdateProfileSavesChanges(): void
    {
        $user = User::fromId(1);
        $user->setUsername('original');
        $user->setEmail('original@example.com');
        $user->setFirstName('Original');
        $user->setLastName('Name');
        $this->userRepository->save($user);

        $user->setFirstName('Updated');
        $user->setLastName('User');
        $user->setEmail('updated@example.com');
        $this->userRepository->save($user);

        $saved = $this->userRepository->UserByUsername('original');
        $this->assertEquals('Updated', $saved->getFirstName());
        $this->assertEquals('User', $saved->getLastName());
        $this->assertEquals('updated@example.com', $saved->getEmail());
    }

    public function testUpdateUsernameChangesKey(): void
    {
        $user = User::fromId(1);
        $user->setUsername('oldname');
        $this->userRepository->save($user);

        $this->assertNotNull($this->userRepository->UserByUsername('oldname'));

        $user->setUsername('newname');
        $this->userRepository->save($user);

        $this->assertNotNull($this->userRepository->UserByUsername('newname'));
    }

    public function testPasswordChangeUpdatesHash(): void
    {
        $user = User::fromId(1);
        $user->setUsername('johndoe');
        $user->setPassword('old_hash');
        $this->userRepository->save($user);

        $user->setPassword('new_hash');
        $this->userRepository->save($user);

        $saved = $this->userRepository->UserByUsername('johndoe');
        $this->assertEquals('new_hash', $saved->getPassword());
        $this->assertNotEquals('old_hash', $saved->getPassword());
    }

    public function testProfileDataIsIndependentPerUser(): void
    {
        $user1 = User::fromId(1);
        $user1->setUsername('user1');
        $user1->setEmail('user1@example.com');
        $this->userRepository->save($user1);

        $user2 = User::fromId(2);
        $user2->setUsername('user2');
        $user2->setEmail('user2@example.com');
        $this->userRepository->save($user2);

        $saved1 = $this->userRepository->UserByUsername('user1');
        $saved2 = $this->userRepository->UserByUsername('user2');

        $this->assertEquals('user1@example.com', $saved1->getEmail());
        $this->assertEquals('user2@example.com', $saved2->getEmail());
    }
}
