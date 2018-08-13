<?php


namespace App\Application\UseCases\User;

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
     * @param $userRepository
     * @param $userPasswordEncoderInterface
     */
    public function __construct($userRepository, $userPasswordEncoderInterface)
    {
        $this->userRepository = $userRepository;
        $this->userPasswordEncoderInterface = $userPasswordEncoderInterface;
    }

    /**
     * @param $user
     * @param $password
     */
    public function execute($user,$password)
    {
        $password = $this->userPasswordEncoderInterface->encodePassword($user, $password);
        $user->setPassword($password);
        $userRepository = new DoctrineUserRepository();
        $userRepository->save($user);
    }
}