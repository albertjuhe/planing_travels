<?php

namespace App\UI\Controller\http;

use App\Domain\User\Exceptions\UserDoesntExists;
use App\Domain\User\Model\User;
use App\Infrastructure\UserBundle\Form\ChangePasswordType;
use App\Infrastructure\UserBundle\Form\ProfileType;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    /** @var DoctrineUserRepository */
    private $userRepository;

    /** @var UserPasswordHasherInterface */
    private $passwordHasher;

    public function __construct(
        DoctrineUserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/{_locale}/private/profile', name: 'profile_edit')]
    public function edit(Request $request, $_locale): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            throw new UserDoesntExists();
        }

        $profileForm = $this->createForm(ProfileType::class, $user);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $this->userRepository->save($user);
            $this->addFlash('notice', 'Profile updated successfully.');

            return $this->redirectToRoute('profile_edit', ['_locale' => $_locale]);
        }

        $passwordForm = $this->createForm(ChangePasswordType::class);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $currentPassword = $passwordForm->get('currentPassword')->getData();

            if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Current password is incorrect.');
            } else {
                $newPassword = $passwordForm->get('newPassword')->getData();
                $encoded = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($encoded);
                $this->userRepository->save($user);
                $this->addFlash('notice', 'Password changed successfully.');
            }

            return $this->redirectToRoute('profile_edit', ['_locale' => $_locale]);
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView(),
        ]);
    }
}
