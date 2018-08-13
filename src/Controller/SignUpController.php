<?php


namespace App\Controller;

use App\Domain\User\Model\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\UseCases\User\SignUpUserService;

class SignUpController extends Controller
{
    /**
     *
     * @Route("/{_locale}/register",name="private_register")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $userRepository = new DoctrineUserRepository();
        $signUpUserService = new SignUpUserService($userRepository,$passwordEncoder);

        $user = new User();
        $form = $this->createForm(UserType::class, $user,array('attr'=>array('class'=>'form-signin')));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $signUpUserService->execute($user,$user->getPlainPassword());
            return $this->redirectToRoute('main_private');
        }
        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView(),'errors'=>$form->getErrors(true, false))
        );
    }
}