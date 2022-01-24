<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

use Capsule\Di\Container;
use Otto\Sapi\Cli\Reporter\ConsoleReporter;

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
            $class = $this->commandClass($argv);
            $optv = $this->options($class, $argv);
            $command = $this->newCommand($class);
            return $command($optv, ...$argv);
        } catch (Throwable $e) {
            return ($this->consoleReporter)($e);
        }
    }

    protected function commandClass(array &$argv) : string
    {
        $console = array_shift($argv);
        $namespace = $this->inflectNamespace(array_shift($argv));
        $subclass = $this->inflectSubclass(array_shift($argv));
        return "{$namespace}\\Sapi\\Cli\\Command\\{$subclass}";
    }

    protected function inflectNamespace(string $str) : string
    {
        return str_replace('-', '', ucfirst(ucwords($str, '-')));
    }

    protected function inflectSubclass(string $str) : string
    {
        return str_replace(':', '\\', $this->inflectNamespace($str));
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
