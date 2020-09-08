<?php

namespace Buki\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateMiddlewareCommand
 *
 * @package Buki\Commands
 */
class CreateMiddlewareCommand extends Command
{
    protected static $defaultName = 'make:middleware';

    protected function configure()
    {
        $this->setDescription('Create a new Middleware');
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of the middleware');
        $this->addArgument('path', InputArgument::OPTIONAL, 'Absolute path of the middleware\'s directory');
        $this->addArgument('namespace', InputArgument::OPTIONAL, 'Namespace of the middleware directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('name');

        if (!is_dir($directory = $input->getArgument('path'))) {
            if (is_null($directory)) {
                $directory = explode('/vendor/', getcwd())[0] . '/Middlewares';

                $output->writeln([
                    '',
                    'Default directory is: ' . $directory,
                ]);
            } else {
                try {
                    mkdir($directory);
                } catch (\Exception $exception) {
                    throw new \Exception($exception->getMessage());
                }
            }
        }

        if (is_null($namespace = $input->getArgument('namespace'))) {
            $namespace = 'Middlewares';

            $output->writeln([
                '',
                'Default namespace is: ' . $namespace,
            ]);
        }

        $filePath = $directory . "/{$className}.php";
        if (is_file($filePath)) {
            throw new \Exception('The middleware already exists!');
        }

        try {
            ob_start();
            echo '<?php';
            include __DIR__ . '/MiddlewareTemplate.php';
            $content = ob_get_contents();
            ob_end_clean();

            $file = fopen($filePath, 'w+');

            file_put_contents($filePath, $content);
            fclose($file);

            $output->writeln([
                '',
                'SUCCESS!',
            ]);

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln([
                '',
                'ERROR: ' . $exception->getMessage(),
            ]);

            return Command::FAILURE;
        }
    }
}
