<?php

declare(strict_types=1);

namespace App\Tests\Components\Shop\Communication\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartPageControllerTest extends WebTestCase
{
    public function testShoppingCartPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/cart-page');
        self::assertSelectorTextContains('h1', 'Shopping Cart');
    }

    public function testEmptyShoppingCartPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/cart-page');
        self::assertSelectorTextNotContains('h1', 'Shopping Cart');

    }
}
