<?php

namespace App\Tests\Domain\User\Model;

use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;

class ProfileTest extends TestCase
{
    public function testUpdateProfileData(): void
    {
        $user = User::fromId(1);
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setEmail('john@example.com');
        $user->setUsername('johndoe');

        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
        $this->assertEquals('john@example.com', $user->getEmail());
        $this->assertEquals('johndoe', $user->getUsername());
    }

    public function testUpdateEmail(): void
    {
        $user = User::fromId(1);
        $user->setEmail('original@example.com');
        $this->assertEquals('original@example.com', $user->getEmail());

        $user->setEmail('updated@example.com');
        $this->assertEquals('updated@example.com', $user->getEmail());
    }

    public function testUpdateUsername(): void
    {
        $user = User::fromId(1);
        $user->setUsername('original');
        $this->assertEquals('original', $user->getUsername());

        $user->setUsername('updated');
        $this->assertEquals('updated', $user->getUsername());
    }

    public function testPasswordCanBeChanged(): void
    {
        $user = User::fromId(1);
        $user->setPassword('hashed_old_password');
        $this->assertEquals('hashed_old_password', $user->getPassword());

        $user->setPassword('hashed_new_password');
        $this->assertEquals('hashed_new_password', $user->getPassword());
    }

    public function testPasswordIsNotEqualToUsername(): void
    {
        $user = new User();
        $user->setUsername('johndoe');
        $user->setFirstName('John');

        $user->setPlainPassword('johndoe');
        $this->assertFalse($user->isPasswordCorrect());
    }

    public function testPasswordIsNotEqualToFirstName(): void
    {
        $user = new User();
        $user->setUsername('johndoe');
        $user->setFirstName('John');

        $user->setPlainPassword('John');
        $this->assertFalse($user->isPasswordCorrect());
    }

    public function testValidPasswordIsAccepted(): void
    {
        $user = new User();
        $user->setUsername('johndoe');
        $user->setFirstName('John');

        $user->setPlainPassword('securepassword123');
        $this->assertTrue($user->isPasswordCorrect());
    }
}
