<?php

namespace App\Components\ResetPassword\Communication\Controller;

use App\Components\ResetPassword\Persistence\Form\ChangePasswordFormType;
use App\Components\ResetPassword\Persistence\Form\ResetPasswordRequestFormType;
use App\Components\User\Business\UserBusinessFacade;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserBusinessFacade $userBusinessFacade,
    ) {
    }

    #[Route('/request', name: 'app_forgot_password_request', methods: ['POST'])]
    public function request(Request $request, MailerInterface $mailer, TranslatorInterface $translator): JsonResponse
    {
        try {
            $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Invalid JSON format',
            ], 400);
        }


        if (empty($content['email']) || !filter_var($content['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Missing or invalid email address',
            ], Response::HTTP_BAD_REQUEST);
        }
        $email = $content['email'];

        $user = $this->userBusinessFacade->getUserEntityByMail($email);
        if (!$user) {
            return $this->json([
                'status' => 'success',
                'message' => 'if there is a existing user with this email, a request will be sent.',
            ], Response::HTTP_OK);
        }


        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'generate reset token failed',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $emailBody = '
    <h1>Hi!</h1>
    <p>To reset your password, please visit the following link:</p>
    <p><a href="http://localhost:4321/resetPassword/' . $resetToken->getToken() . '">Reset password</a></p>
    <p>This link will expire in ' . $resetToken->getExpirationMessageKey() . '.</p>
    <p>Cheers!</p>
';

        $email = (new TemplatedEmail())
            ->from(new Address('resetPassword@footballapi.com'))
            ->to($user->getUserIdentifier())
            ->subject('Your password reset request')
            ->html($emailBody);

        $mailer->send($email);

        return new JsonResponse([
            'message' => 'Password reset link sent.',
        ], Response::HTTP_OK);
    }

    #[Route('/validateToken', name: 'app_reset_password_validate_token', methods: ['POST'])]
    public function validateToken(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;

        if (!$token) {
            return new JsonResponse([
                'tokenValidation' => false,
                'message' => 'Token is missing.',
            ], 200);
        }


        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse([
                'tokenValidation' => false,
                'message' => "bananna" . $e->getMessage(),
            ], status: 200);
        }

        if (!$user instanceof User) {
            return new JsonResponse([
                'tokenValidation' => false,
                'message' => 'Invalid or expired token.',
            ], 200);
        }

        return new JsonResponse([
            'tokenValidation' => true,
            'message' => 'Token is valid.',
        ], 200);
    }


    /*
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route('/reset/', name: 'app_reset_password', methods: ['POST']) ]
    public function resetPassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($data['token'])){
           return new JsonResponse([
               'status' => 'error',
               'message' => 'Token is missing.',
           ], Response::HTTP_UNAUTHORIZED);
        }
        if (!isset($data['password']) || !preg_match(
                "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/",
                $data['password']
            )) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Password must be at least 6 characters long and include at least one uppercase letter, one 
                lowercase letter, one number, and one special character.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->resetPasswordHelper->validateTokenAndFetchUser($data['token']);

        if (!$user instanceof User) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid or expired token.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $this->resetPasswordHelper->removeResetRequest($data['token']);
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Password successfully reset.',
        ], Response::HTTP_OK);
    }
    /*
        $email = (new TemplatedEmail())
            ->from(new Address('service@banana.de', 'Mailer'))
            ->to((string)$user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $mailer->send($email);
*/
    // Store the token object in session for retrieval in check-email route.


}
