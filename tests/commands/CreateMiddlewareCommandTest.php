<?php

namespace Buki\Tests\Commands;

use Buki\Commands\CreateMiddlewareCommand;
use Buki\Commands\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class CreateMiddlewareCommandTest
 *
 * @package Buki\Tests\Commands
 */
class CreateMiddlewareCommandTest extends TestCase
{
    /** @var CommandTester */
    private $commandTester;

    protected function setUp()
    {
        $application = new Application();
        $application->add(new CreateMiddlewareCommand());
        $command = $application->find('make:middleware');
        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        $this->commandTester->execute([
            'name' => 'TestMiddleware',
            'path' => __DIR__ . '/Middlewares',
            'namespace' => 'Middlewares',
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

    public function testExecuteShouldThrowExceptionForExistentMiddleware()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The middleware already exists!');

        $this->commandTester->execute([
            'name' => 'TestMiddleware',
            'path' => __DIR__ . '/Middlewares',
            'namespace' => 'Middlewares',
        ]);
    }

    public function testExecuteShouldThrowExceptionForMkdirPermissionDenied()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('mkdir(): Permission denied');

        $this->commandTester->execute([
            'name' => 'TestMiddleware',
            'path' => '/SomeDirectory',
            'namespace' => 'Middlewares',
        ]);
    }
}
