<?php
namespace Otto\Sapi\Cli;

class ConsoleTest extends TestCase
{
    public function test()
    {
        $console = $this->container->new(Console::CLASS);
        $argv = ['./bin/console', 'fake-project', 'fake-cmd'];
        $result = $console($argv);
        var_dump($result);
    }
}
