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
        $this->assertTrue($user->equal($newUser));

        $newUser = User::fromId(4);
        $this->assertFalse($user->equal($newUser));
    }
}