<?php

declare(strict_types=1);

namespace App\Tests\Components\UserRegister\Communication;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testRegisterPageLoadsSuccessfully(): void
    {
        $this->client->request('GET', '/register-page');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form[name="register_form"]');
    }

    public function testRegisterWithValidData(): void
    {
        $crawler = $this->client->request('GET', '/register-page');

        self::assertResponseStatusCodeSame(200);

        $form = $crawler->filter('form[name="register_form"]')->form();

        $formData = [
            'register_form[firstName]' => 'John',
            'register_form[lastName]' => 'Doe',
            'register_form[email]' => 'john.doe@example.com',
            'register_form[password][first]' => 'Password123!',
            'register_form[password][second]' => 'Password123!',
        ];

        $this->client->submit($form, $formData);

        if (422 === $this->client->getResponse()->getStatusCode()) {
            echo $this->client->getResponse()->getContent();

            // $crawler = $this->client->followRedirect();

            self::assertSelectorTextContains('.form-error', 'This value should not be blank');

            self::assertResponseRedirects('/login');
        }
    }

    public function testRegisterWithInvalidDataTwo(): void
    {
        $this->client->request('GET', '/register-page');
        self::assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/register-page');
        $csrfToken = $crawler->filter('input[name="register_form[_token]"]')->attr('value');

        //  dd($this->client->getResponse()->getContent());

        $this->client->submitForm('register_form[submit]', [
            'register_form[firstName]' => '',
            'register_form[lastName]' => '',
            'register_form[email]' => 'invalid-email',
            'register_form[password][first]' => '',
            'register_form[password][second]' => '',
            'register_form[_token]' => $csrfToken,
        ]);

        self::assertSelectorTextContains('ul li', 'This value should not be blank.', 'First name error not found.');
        self::assertSelectorTextContains('ul li', 'This value should not be blank.', 'Last name error not found.');
        self::assertSelectorTextContains('ul li', 'This value should not be blank.', 'Email error not found.');
        self::assertSelectorTextContains('ul li', 'This value should not be blank.', 'Password error not found.');
    }

    public function testRegisterWithMismatchedPasswords(): void
    {
        $this->client->request('GET', '/register-page');
        self::assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/register-page');
        $csrfToken = $crawler->filter('input[name="register_form[_token]"]')->attr('value');

        //  dd($this->client->getResponse()->getContent());

        $this->client->submitForm('register_form[submit]', [
            'register_form[firstName]' => 'htrjr',
            'register_form[lastName]' => 'ht',
            'register_form[email]' => 'invalid-email',
            'register_form[password][first]' => 'gaeg',
            'register_form[password][second]' => '',
            'register_form[_token]' => $csrfToken,
        ]);

        $content = $this->client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422);
        self::assertStringContainsString(
            'Please enter a valid email address', $content, 'Email error message found in the response.');

    }
}
