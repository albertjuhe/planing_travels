<?php

namespace App\Tests\Application\Command;

use App\Application\Command\User\SignInUserCommand;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;

class SignInUserCommandTest extends TestCase
{
    public function testSignInUserCommand()
    {
        $user = $this->createMock(User::class);
        $signInUserCommand = new SignInUserCommand($user);
        $this->assertEquals($user, $signInUserCommand->getUser());
    }
}
