<?php


namespace App\Tests\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\User\Model\User;

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
    public function testEqual() {
        $user = User::fromId(1);
        $newUser = User::fromId(1);

        $this->assertTrue($user->equalsTo($newUser));

        $newUser = User::fromId(4);
        $this->assertFalse($user->equalsTo($newUser));
    }

    public function testSettersGetters() {
        $user = User::fromId(1);

        $user->setEmail('email@email.com');
        $this->assertEquals('email@email.com',$user->getEmail());

        $user->setFirstName('firstname');
        $this->assertEquals('firstname',$user->getFirstName());

        $user->setIsActive(true);
        $this->assertTrue($user->getIsActive());

        $user->setIsActive(false);
        $this->assertFalse($user->getIsActive());

        $user->setLastLogin(new \DateTime('2018-01-01'));
        $this->assertEquals(new \DateTime('2018-01-01'),$user->getLastLogin());

        $user->setCreatedAt(new \DateTime('2018-01-01'));
        $this->assertEquals(new \DateTime('2018-01-01'),$user->getCreatedAt());

        $user->setUpdatedAt(new \DateTime('2018-01-01'));
        $this->assertEquals(new \DateTime('2018-01-01'),$user->getUpdatedAt());

        $user->setLocale('en');
        $this->assertEquals('en',$user->getLocale());

        $user->setLastName('lastname');
        $this->assertEquals('lastname',$user->getLastName());

        $user->setPassword('xxxxx');
        $this->assertEquals('xxxxx',$user->getPassword());

        $user->setPlainPassword('xxxxx');
        $this->assertEquals('xxxxx',$user->getPlainPassword());

        $user->setUsername('username');
        $this->assertEquals('username',$user->getUsername());

    }
}