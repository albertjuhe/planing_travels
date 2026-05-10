<?php

namespace App\UI\Controller\http;

use App\Domain\User\Model\User;
use App\Infrastructure\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\UseCases\User\SignUpUserService;

class SignUpController extends AbstractController
{
    /** @var DoctrineUserRepository */
    private $userRepository;

    /**
     * SignUpController constructor.
     *
     * @param $userRepository
     */
    public function __construct(DoctrineUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/{_locale}/register', name: 'private_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['attr' => ['class' => 'form-signin']]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $signUpUserService = new SignUpUserService($this->userRepository, $passwordHasher);
            $signUpUserService->execute($user);

            return $this->redirectToRoute('main_private');
        }

        return $this->render(
            'security/register.html.twig',
            ['form' => $form->createView(), 'errors' => $form->getErrors(true, false)]
        );
    }
}
