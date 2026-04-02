<?php

namespace App\Tests\Domain\User\Model;

use App\Domain\User\Model\PasswordResetToken;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;

class PasswordResetTokenTest extends TestCase
{
    public function testTokenCanBeUsedWhenCreated()
    {
        $user = new User();
        $token = new PasswordResetToken($user, hash('sha256', 'abc'), new \DateTime('+1 hour'));

        $this->assertTrue($token->canBeUsed());
    }

    public function testTokenCannotBeUsedAfterMarkedAsUsed()
    {
        $user = new User();
        $token = new PasswordResetToken($user, hash('sha256', 'abc'), new \DateTime('+1 hour'));

        $token->markAsUsed();

        $this->assertFalse($token->canBeUsed());
    }
}

