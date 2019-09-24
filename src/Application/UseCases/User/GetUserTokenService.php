<?php

namespace App\Application\UseCases\User;

use App\Domain\Common\Model\ApiCodes;
use App\Domain\User\Exceptions\BadCredentialsException;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GetUserTokenService
{
    const HOUR = 3600;
    /**
     * @var JWTEncoderInterface
     */
    private $JWTEncoder;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $userPasswordEncoderInterface,
        JWTEncoderInterface $JWTEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->userPasswordEncoderInterface = $userPasswordEncoderInterface;
        $this->JWTEncoder = $JWTEncoder;
    }

    public function execute(string $username, string $password)
    {
        $user = $this->userRepository->UserByUsername($username);

        if (!$user instanceof User) {
            throw new UserDoesntExists(
                ApiCodes::BAD_REQUEST,
                'User doesnt exists'
            );
        }

        $isValid = $this->userPasswordEncoderInterface->isPasswordValid($user, $password);

        if (!$isValid) {
            throw new BadCredentialsException(
                ApiCodes::UNAUTHORIZED,
                'Bad credentials'
            );
        }

        return $this->JWTEncoder->encode([
            'username' => $username,
            'exp' => time() + self::HOUR,
        ]);
    }
}
