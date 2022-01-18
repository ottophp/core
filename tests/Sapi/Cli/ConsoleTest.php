<?php
namespace Otto\Sapi\Cli;

class ConsoleTest extends TestCase
{
    public function test()
    {
        $console = $this->container->new(Console::CLASS);
        $argv = ['/path/to/php', 'fake-project', 'fake-cmd'];
        $result = $console($argv);
        var_dump($result);
    }
}
