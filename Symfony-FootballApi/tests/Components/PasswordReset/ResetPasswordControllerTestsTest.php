<?php

declare(strict_types=1);

namespace App\Tests\Components\PasswordReset;

use App\Tests\BaseWebTestCase;
use App\Tests\Fixtures\Config;
use Symfony\Component\HttpFoundation\Response;


class ResetPasswordControllerTestsTest extends BaseWebTestCase
{
    public function testRequestPasswordResetForm(): void
    {
        $client = static::getClient();
        $client->request('GET', '/reset-password');

        // Assert that the response is successful
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Reset your password');
        self::assertSelectorExists('form[name="reset_password_request_form"]');
    }

    public function testRequestPasswordResetWithValidEmail(): void
    {
        $client = static::getClient();

        // Submit the form with an invalid email
        $client->request('GET', '/reset-password');
        $client->submitForm('Send password reset email', [
            'reset_password_request_form[email]' => Config::USER_EMAIL_ONE,
        ]);

        self::assertResponseRedirects('/reset-password/check-email');

        $client->followRedirect();
        $htmlContent = $client->getResponse()->getContent();

        self::assertStringContainsString(
            'email was just sent that contains a link that you can use to reset your password', $htmlContent);
    }

    public function testRequestPasswordResetWithInvalidEmail(): void
    {
        $client = static::getClient();

        // Submit the form with an invalid email
        $client->request('GET', '/reset-password');
        $client->submitForm('Send password reset email', [
            'reset_password_request_form[email]' => 'nonexistent@example.com',
        ]);

        self::assertResponseRedirects('/reset-password/check-email');

        $client->followRedirect();
        $htmlContent = $client->getResponse()->getContent();

        self::assertStringContainsString(
            'email was just sent that contains a link that you can use to reset your password', $htmlContent);
    }

    public function testCheckEmailPage(): void
    {
        $client = static::getClient();
        $crawler = $client->request('GET', '/reset-password/check-email');

        // Assert that the response is successful

        self::assertSelectorTextContains(
            'p',
            'If an account matching your email exists, then an email was just sent that contains a link that you can use to reset your password.'
        );
    }

    /*
        public function testResetPasswordFormWithValidToken()
        {
            $client = static::createClient();

            // Create a mock user
            $user = (new User())
                ->setEmail('test@example.com')
                ->setPassword('hashedpassword');

            $entityManager = self::$container->get('doctrine')->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Generate a reset token for the user
            $resetToken = 'valid_token';

            // Simulate the token being stored in the session
            $session = $client->getContainer()->get('session');
            $session->set('_security_main', serialize(new UsernamePasswordToken($user, 'main', 'main', [])));
            $session->save();

            // Submit a valid password reset form
            $client->request('POST', '/reset-password/reset/'.$resetToken, [
                'change_password_form[plainPassword]' => 'newpassword123',
            ]);

            // Assert the user is redirected to the login page after password reset
            self::assertResponseRedirects('/login');
        }

    */
    /*
 public function testResetPasswordWithInvalidToken(): void
 {
     $client = static::getClient();

     // Invalid token
     $invalidToken = 'invalid_token';

     // Try to access the reset password form with an invalid token
     $client->request('GET', '/reset-password/reset/'.$invalidToken);

     // Assert that the exception page is shown
   //  self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
 }

     public function testProcessSendingPasswordResetEmail(): void
     {
         $client = static::createClient();

         $mailerMock = $this->createMock(MailerInterface::class);
         $translatorMock = $this->createMock(TranslatorInterface::class);

         $mailerMock->expects($this->once())
             ->method('send')
             ->willReturn(true);

         // Set up the form data
         $email = 'test@example.com';

         // Simulate form submission
         $client->request('POST', '/reset-password', [
             'reset_password_request_form[email]' => $email,
         ]);


         $controller = new \App\Components\ResetPassword\Communication\Controller\ResetPasswordController(
             $this->createMock(ResetPasswordHelperInterface::class),
             $this->createMock(EntityManagerInterface::class)
         );
     }
 */
}
