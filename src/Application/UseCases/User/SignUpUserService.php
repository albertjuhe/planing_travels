<?php

namespace App\Application\UseCases\User;

use App\Domain\User\Model\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Domain\User\Repository\UserRepository;

class SignUpUserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoderInterface;

    /**
     * SignUpUserService constructor.
     *
     * @param $userRepository
     * @param $userPasswordEncoderInterface
     */
    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $userPasswordEncoderInterface
    ) {
        $this->userRepository = $userRepository;
        $this->userPasswordEncoderInterface = $userPasswordEncoderInterface;
    }

    /**
     * @param $user
     * @param $password
     */
    public function execute(User $user)
    {
        $password = $this->userPasswordEncoderInterface->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        $this->userRepository->save($user);
    }
}
