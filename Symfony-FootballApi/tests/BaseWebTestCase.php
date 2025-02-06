<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;

class BaseWebTestCase extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        static::createClient();
        $this->resetDatabase();
    }

    private function resetDatabase(): void
    {
        $application = new Application(static::$kernel);
        $application->setAutoExit(false);

        // Run the database reset commands
        $commands = [
            ['command' => 'doctrine:database:drop', '--force' => true, '--env' => 'test', '--quiet' => true],
            ['command' => 'doctrine:database:create', '--env' => 'test', '--quiet' => true],
            ['command' => 'doctrine:schema:update', '--force' => true, '--env' => 'test', '--quiet' => true],
            ['command' => 'doctrine:fixtures:load', '--env' => 'test', '--no-interaction' => true, '--quiet' => true],
        ];

        foreach ($commands as $command) {
            $application->run(new ArrayInput($command));
        }
    }
}
