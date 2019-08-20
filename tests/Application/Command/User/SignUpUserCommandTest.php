<?php

namespace App\Tests\Application\Command;

use App\Application\Command\User\SignUpUserCommand;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;

class SignUpUserCommandTest extends TestCase
{
    public function testSignInUserCommand()
    {
        $user = $this->createMock(User::class);
        $signUpUserCommand = new SignUpUserCommand($user);
        $this->assertEquals($user, $signUpUserCommand->getUser());
    }
}
