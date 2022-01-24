<?php
namespace Otto\Sapi\Cli;

class ConsoleTest extends TestCase
{
    public function test()
    {
        $console = $this->container->new(Console::CLASS);
        $argv = ['./bin/console', 'fake-project', 'hello', 'zim'];
        $result = $console($argv);
        $this->assertSame(0, $result->getCode());
        $this->assertSame("Hello, zim!" . PHP_EOL, $result->getOutput());
    }
}
