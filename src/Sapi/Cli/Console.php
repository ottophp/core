<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

use Capsule\Di\Container;
use Otto\Sapi\Cli\Reporter\ConsoleReporter;
use Throwable;

class Console
{
    public function __construct(
        protected Container $container,
        protected ConsoleReporter $consoleReporter
    ) {
    }

    public function __invoke(array $argv) : Result
    {
        try {
            $console = array_shift($argv);
            $class = $this->commandClass($argv);
            $optv = $this->options($class, $argv);
            // $argv should now get fixed as per __invoke() params.
            // cf. AutoRoute and its parsing system for param/args.
            //
            // this is starting to look extractable, a la AutoGetopt
            // or something like that.
            $command = $this->newCommand($class);
            return $command($optv, ...$argv);
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

    protected function options(string $class, array &$argv) : array
    {
        $getopt = Getopt::new($class, '__invoke');
        return $getopt->parse($argv);
    }

    protected function newCommand(string $class) : object
    {
        return $this->container->new($class);
    }
}
