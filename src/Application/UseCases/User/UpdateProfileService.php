<?php

namespace App\Application\UseCases\User;

use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepository;

class UpdateProfileService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(User $user): void
    {
        $this->userRepository->save($user);
    }
}
