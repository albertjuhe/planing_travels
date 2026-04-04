<?php

namespace App\UI\Controller\http;

use App\Domain\User\Model\PasswordResetToken;
use App\Domain\User\Model\User;
use App\Infrastructure\UserBundle\Form\ForgotPasswordRequestType;
use App\Infrastructure\UserBundle\Form\ResetPasswordType;
use App\Infrastructure\UserBundle\Repository\DoctrinePasswordResetTokenRepository;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordResetController extends AbstractController
{
    /**
     * @Route("/{_locale}/forgot-password", name="forgot_password")
     */
    public function forgotPassword(
        Request $request,
        DoctrineUserRepository $userRepository,
        DoctrinePasswordResetTokenRepository $tokenRepository,
        $_locale
    ): Response {
        $form = $this->createForm(ForgotPasswordRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = (string) $form->get('email')->getData();
            $user = $userRepository->userByEmail($email);

            if ($user instanceof User) {
                $tokenRepository->invalidateActiveUserTokens($user);

                $plainToken = bin2hex(random_bytes(32));
                $expiresAt = new \DateTime('+1 hour');
                $token = new PasswordResetToken($user, hash('sha256', $plainToken), $expiresAt);
                $tokenRepository->save($token);

                $resetUrl = $this->generateUrl('reset_password', [
                    '_locale' => $_locale,
                    'token' => $plainToken,
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                if ($this->getParameter('kernel.debug')) {
                    // In local/dev environments this helps to test the full reset flow without SMTP.
                    $this->addFlash('notice', sprintf('DEV reset link: %s', $resetUrl));
                }

                if (function_exists('mail')) {
                    $subject = 'JuheTravel - Password reset';
                    $message = "You requested a password reset.\n\n";
                    $message .= "Open this link to continue:\n".$resetUrl."\n\n";
                    $message .= "If you did not request this, just ignore this email.";

                    @mail($user->getEmail(), $subject, $message, 'From: no-reply@juhetravel.local');
                }
            }

            $this->addFlash('notice', 'If an account exists for that email, we sent a reset link.');

            return $this->redirectToRoute('forgot_password', ['_locale' => $_locale]);
        }

        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/reset-password/{token}", name="reset_password")
     */
    public function resetPassword(
        Request $request,
        string $token,
        DoctrinePasswordResetTokenRepository $tokenRepository,
        DoctrineUserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        $_locale
    ): Response {
        $resetToken = $tokenRepository->findValidTokenByPlainValue($token);

        if (!$resetToken instanceof PasswordResetToken) {
            $this->addFlash('error', 'Reset link is invalid or has expired.');

            return $this->redirectToRoute('forgot_password', ['_locale' => $_locale]);
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = (string) $form->get('newPassword')->getData();
            $user = $resetToken->getUser();
            $encoded = $passwordEncoder->encodePassword($user, $newPassword);

            $user->setPassword($encoded);
            $resetToken->markAsUsed();

            $userRepository->save($user);
            $tokenRepository->save($resetToken);

            $this->addFlash('notice', 'Your password has been updated. You can now log in.');

            return $this->redirectToRoute('private_login', ['_locale' => $_locale]);
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
