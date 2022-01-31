<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

use Capsule\Di\Container;
use Otto\Sapi\Cli\Getopt;
use Otto\Sapi\Cli\Options;
use Otto\Sapi\Cli\Reporter\ConsoleReporter;
use Throwable;

class Console
{
    public function __construct(
        protected Container $container,
        protected Getopt $getopt,
        protected ConsoleReporter $consoleReporter
    ) {
    }

    public function __invoke(array $argv) : Result
    {
        try {
            $console = array_shift($argv);
            $class = $this->commandClass($argv);
            $options = Options::new($class, '__invoke');
            $arguments = $this->getopt->parse($argv, $options);

            // $arguments should now get fixed/typed as per __invoke() params.
            // cf. AutoRoute and its parsing system for param/args.

            $command = $this->newCommand($class);
            return $command($options, ...$arguments);
        } catch (Throwable $e) {
            return ($this->consoleReporter)($command ?? null, $e);
        }
    }

    protected function commandClass(array &$argv) : string
    {
        $namespace = $this->inflectNamespace(array_shift($argv));
        $subclass = $this->inflectSubclass(array_shift($argv));
        $class = "{$namespace}\\Sapi\\Cli\\Command\\{$subclass}";

        if (! class_exists($class)) {
            throw new Exception\CommandNotFound($class);
        }

        return $class;
    }

    protected function inflectNamespace(string $str) : string
    {
        return str_replace('-', '', ucfirst(ucwords($str, '-')));
    }

    protected function inflectSubclass(string $str) : string
    {
        return str_replace(
            DIRECTORY_SEPARATOR,
            '\\',
            $this->inflectNamespace($str)
        );
    }

    protected function newCommand(string $class) : object
    {
        return $this->container->new($class);
    }
}
