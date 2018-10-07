<?php


namespace App\Controller;

use App\Domain\User\Model\User;
use App\Infrastructure\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\UseCases\User\SignUpUserService;

class SignUpController extends Controller
{
    /** @var DoctrineUserRepository  */
    private $userRepository;

    /**
     * SignUpController constructor.
     * @param $userRepository
     */

    public function __construct(DoctrineUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

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
        $user = new User();
        $form = $this->createForm(UserType::class, $user,array('attr'=>array('class'=>'form-signin')));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $signUpUserService = new SignUpUserService($this->userRepository,$passwordEncoder);
            $signUpUserService->execute($user);
            return $this->redirectToRoute('main_private');
        }
        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView(),'errors'=>$form->getErrors(true, false))
        );
    }
}