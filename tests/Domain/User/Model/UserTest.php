<?php

namespace App\Tests\Domain\User\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\User\Model\User;
use App\Domain\User\ValueObject\UserId;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\ValueObject\GeoLocation;

class UserTest extends TestCase
{
    public function testIsPasswordCorrect()
    {
        $user = new User();
        $user->setFirstName('albert');
        $user->setUsername('ajuhe');

        $user->setPlainPassword('ajuhe');
        $this->assertFalse($user->isPasswordCorrect());

        $user->setPlainPassword('albert');
        $this->assertFalse($user->isPasswordCorrect());

        $user->setPlainPassword('password');
        $this->assertTrue($user->isPasswordCorrect());
    }

    public function testEqual()
    {
        $id = mt_rand();
        $user = User::fromId($id);
        $newUser = User::fromId($id);

        $this->assertTrue($user->equalsTo($newUser));

        $newUser = User::fromId($id + 1);
        $this->assertFalse($user->equalsTo($newUser));
    }

    public function testSettersGetters()
    {
        $user = User::fromId(1);

        $user->setEmail('email@email.com');
        $this->assertEquals('email@email.com', $user->getEmail());

        $user->setFirstName('firstname');
        $this->assertEquals('firstname', $user->getFirstName());

        $user->setIsActive(true);
        $this->assertTrue($user->getIsActive());

        $user->setIsActive(false);
        $this->assertFalse($user->getIsActive());

        $user->setLastLogin(new \DateTime('2018-01-01'));
        $this->assertEquals(new \DateTime('2018-01-01'), $user->getLastLogin());

        $user->setCreatedAt(new \DateTime('2018-01-01'));
        $this->assertEquals(new \DateTime('2018-01-01'), $user->getCreatedAt());

        $user->setUpdatedAt(new \DateTime('2018-01-01'));
        $this->assertEquals(new \DateTime('2018-01-01'), $user->getUpdatedAt());

        $user->setLocale('en');
        $this->assertEquals('en', $user->getLocale());

        $user->setLastName('lastname');
        $this->assertEquals('lastname', $user->getLastName());

        $user->setPassword('xxxxx');
        $this->assertEquals('xxxxx', $user->getPassword());

        $user->setPlainPassword('xxxxx');
        $this->assertEquals('xxxxx', $user->getPlainPassword());

        $user->setUsername('username');
        $this->assertEquals('username', $user->getUsername());
    }

    public function testSerialize(): void
    {
        $user = User::fromId(1);
        $user->setUsername('usernameTest');
        $user->setPassword('passwordTest');

        $data = $user->__serialize();
        $this->assertInstanceOf(UserId::class, $data[0]);
        $this->assertSame('usernameTest', $data[2]);
        $this->assertSame('passwordTest', $data[3]);
    }

    public function testUnserialize(): void
    {
        $userId = new UserId(1);
        $user = User::fromId(1);
        $user->__unserialize([$userId, null, 'usernameTest', 'passwordTest']);

        $this->assertEquals('usernameTest', $user->getUsername());
        $this->assertEquals('passwordTest', $user->getPassword());
        $this->assertEquals(1, $user->getId()->id());
    }

    public function testGetRoles()
    {
        $user = new User();
        $roles = $user->getRoles();

        $this->assertCount(1, $roles);
        $this->assertEquals('ROLE_USER', $roles[0]);
    }

    public function testAddTravel()
    {
        $geoLocation = new GeoLocation(10, 20, 30, 40, 50, 60);
        $user = User::fromId(1);
        $travel = Travel::fromTitleAndGeolocationAndUser('dummyTravel', $geoLocation, $user);
        $user2 = $travel->getUser();
        $this->assertTrue($user2->equalsTo($user));
    }
}
