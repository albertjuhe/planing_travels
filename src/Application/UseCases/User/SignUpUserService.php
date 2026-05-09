<?php

namespace App\Application\UseCases\User;

use App\Domain\User\Model\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Domain\User\Repository\UserRepository;

class SignUpUserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @param $user
     * @param $password
     */
    public function execute(User $user)
    {
        $password = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        $this->userRepository->save($user);
    }
}
