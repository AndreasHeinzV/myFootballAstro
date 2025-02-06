<?php

declare(strict_types=1);

namespace App\Tests\Components\UserRegister\Communication\Form;

use App\Components\UserRegister\Communication\Form\RegisterForm;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class RegisterFormTest extends WebTestCase
{
    private FormFactoryInterface $formFactory;
    private CsrfTokenManagerInterface $csrfTokenManager;


    protected function setUp(): void
    {
        parent::setUp();
        $this->formFactory = self::getContainer()->get(FormFactoryInterface::class);
        $this->csrfTokenManager = self::getContainer()->get(CsrfTokenManagerInterface::class);
    }

    public function testFormHasAllFields(): void
    {
        $form = $this->formFactory->create(RegisterForm::class);
        $this->assertTrue($form->has('firstName'));
        $this->assertTrue($form->has('lastName'));
        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('password'));
        $this->assertTrue($form->has('submit'));
    }

    public function testSubmitInvalidEmail(): void
    {
        $form = $this->formFactory->create(RegisterForm::class);

        $data = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doeexample.com',
            'password' => 'Password123!',
            'password_repeat' => 'Password123!',
        ];

        $form->submit($data);

        $this->assertFalse($form->isValid());

        $errors = $form->get('email')->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame('Please enter a valid email address', $errors[0]->getMessage());
    }


    public function testSubmitInvalidLastNameEmpty(): void
    {
        $form = $this->formFactory->create(RegisterForm::class);

        $data = [
            'firstName' => 'user',
            'lastName' => '',
            'email' => 'john.doe@example.com',
            'password' => 'Password123!',
            'password_repeat' => 'Password123!',
        ];

        $form->submit($data);

        $this->assertFalse($form->isValid());

        $errors = $form->get('lastName')->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame('This value should not be blank.', $errors[0]->getMessage());
    }

}
