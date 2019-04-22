<?php

namespace App\UI\Controller\http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Application\UseCases\User\SignInUserService;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SignInController.
 */
class SignInController extends AbstractController
{
    /**
     * @Route("/{_locale}/login", name="private_login")
     *
     * @param Request             $request
     * @param AuthenticationUtils $authUtils
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        $signInService = new SignInUserService($authUtils);
        $signInService->execute();

        return $this->render('security/login.html.twig', [
            'last_username' => $signInService->getLastUsername(),
            'error' => $signInService->getError(),
        ]);
    }
}
