<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;

class BaseTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Reset the database
        $this->resetDatabase();
    }

    private function resetDatabase(): void
    {
        $kernel = self::bootKernel();

        $application = new Application($kernel);
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