<?php

namespace Buki\Tests\Commands;

use Buki\Commands\CreateControllerCommand;
use Buki\Commands\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class CreateControllerCommandTest
 *
 * @package Buki\Tests\Commands
 */
class CreateControllerCommandTest extends TestCase
{
    /** @var CommandTester */
    private $commandTester;

    protected function setUp()
    {
        $application = new Application();
        $application->add(new CreateControllerCommand());
        $command = $application->find('make:controller');
        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        $this->commandTester->execute([
            'name' => 'TestController',
            'path' => __DIR__ . '/Controllers',
            'namespace' => 'Controllers',
        ]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testExecuteShouldThrowExceptionForEmptyName()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "name")');

        $this->commandTester->execute([
            // name is required
        ]);
    }

    public function testExecuteShouldThrowExceptionForExistentController()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The controller already exists!');

        $this->commandTester->execute([
            'name' => 'TestController',
            'path' => __DIR__ . '/Controllers',
            'namespace' => 'Controllers',
        ]);
    }

    public function testExecuteShouldThrowExceptionForMkdirPermissionDenied()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('mkdir(): Permission denied');

        $this->commandTester->execute([
            'name' => 'TestController',
            'path' => '/SomeDirectory',
            'namespace' => 'Controllers',
        ]);
    }
}
