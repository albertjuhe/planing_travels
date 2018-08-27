<?php
/**
 * Created by PhpStorm.
 * User: Albert
 * Date: 20/01/2018
 * Time: 16:23
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Application\UseCases\User\SignInUserService;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SignInController
 * @package App\Controller
 */
class SignInController extends Controller
{
    /**
     * @Route("/{_locale}/login", name="private_login")
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        $signInService = new SignInUserService($authUtils);
        $signInService->execute();

        return $this->render('security/login.html.twig', array(
            'last_username' => $signInService->getLastUsername(),
            'error' => $signInService->getError(),
        ));
    }


}