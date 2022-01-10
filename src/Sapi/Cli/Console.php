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
            $script = array_shift($argv);
            $namespace = $this->inflectNamespace(array_shift($argv));
            $subclass = $this->inflectSubclass(array_shift($argv));
            $class = "{$ns}\\Sapi\\Cli\\Command\\{$subclass}";
            $optv = Getopt::new($class, '__invoke')->parse($argv);
            $command = $container->new($class);
            return $command($optv, ...$argv); // does not blow up with too many $argv -- should it?
        } catch (Throwable $e) {
            return ($this->consoleReporter)($e);
        }
    }

    protected function inflectNamespace(string $str) : string
    {
        return str_replace('-', '', ucfirst(ucwords($name, '-')));
    }


    protected function inflectSubclass(string $str) : string
    {
        return str_replace(':', '\\', $this->inflectNamespace($str));
    }
}
