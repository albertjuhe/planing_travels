<?php

namespace App\Application\UseCases\User;

use App\Domain\User\Model\User;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SignInUserService
{
    /**
     * @var AuthenticationUtils
     */
    private $authUtils;

    /**
     * @var string
     */
    private $error;

    /**
     * @var User
     */
    private $lastUsername;

    /**
     * SignInUserService constructor.
     *
     * @param AuthenticationUtils $authUtils
     */
    public function __construct(AuthenticationUtils $authUtils)
    {
        $this->authUtils = $authUtils;
    }

    /**
     * Sign User.
     */
    public function execute()
    {
        // get the login error if there is one
        $this->error = $this->authUtils->getLastAuthenticationError();
        // last username entered by the user
        $this->lastUsername = $this->authUtils->getLastUsername();
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setError($error): void
    {
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getLastUsername()
    {
        return $this->lastUsername;
    }

    /**
     * @param mixed $lastUsername
     */
    public function setLastUsername($lastUsername): void
    {
        $this->lastUsername = $lastUsername;
    }
}
