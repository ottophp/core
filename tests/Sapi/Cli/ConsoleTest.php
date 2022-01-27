<?php
namespace Otto\Sapi\Cli;

use FakeProject\Domain\Payload;
use Otto\Sapi\Cli\Result;

class ConsoleTest extends TestCase
{
    public function testCommand()
    {
        $console = $this->container->new(Console::CLASS);
        $argv = ['./bin/console', 'fake-project', 'hello', 'zim'];
        $result = $console($argv);
        $this->assertSame(0, $result->getCode());
        $this->assertSame("Hello, zim!" . PHP_EOL, $result->getOutput());
    }

    /**
     * @dataProvider provideStatus
     */
    public function testStatus(string $status, int $code)
    {
        $console = $this->container->new(Console::CLASS);
        $argv = ['./bin/console', 'fake-project', 'hello-status', $status];
        $result = $console($argv);
        $this->assertSame($code, $result->getCode());
        $this->assertSame(
            "The domain payload reported a status of {$status}." . PHP_EOL,
            $result->getOutput()
        );
    }

    public function provideStatus() : array
    {
        return [
            [Payload::ACCEPTED, Result::SUCCESS],
            [Payload::CREATED, Result::SUCCESS],
            [Payload::DELETED, Result::SUCCESS],
            [Payload::ERROR, Result::FAILURE],
            [Payload::FOUND, Result::SUCCESS],
            [Payload::INVALID, Result::DATAERR],
            [Payload::NOT_FOUND, Result::FAILURE],
            [Payload::PROCESSING, Result::SUCCESS],
            [Payload::SUCCESS, Result::SUCCESS],
            [Payload::UNAUTHORIZED, Result::NOPERM],
            [Payload::UPDATED, Result::SUCCESS],
        ];
    }

    public function testThrowable()
    {
        $console = $this->container->get(Console::CLASS);
        $argv = ['./bin/console', 'fake-project', 'pitch'];
        $result = $console($argv);
        $this->assertSame(Result::FAILURE, $result->getCode());
        $this->assertStringContainsString(
            "LogicException: Fake logic exception thrown.",
            $result->getOutput()
        );
    }

    // public function testCommandNotFound()
    // {
    //     $_SERVER['REQUEST_METHOD'] = 'GET';
    //     $_SERVER['REQUEST_URI'] = '/none-such';
    //     $console = $this->container->get(Console::CLASS);
    //     $result = $console();
    //     $this->assertSame(404, $result->getCode());
    //     $this->assertStringContainsString('Route not found for URL.', $result->getContent());
    // }

    // public function testBadCommandArgument()
    // {
    //     $_SERVER['REQUEST_METHOD'] = 'GET';
    //     $_SERVER['REQUEST_URI'] = '/console/route/not-an-int';
    //     $console = $this->container->get(Console::CLASS);
    //     $result = $console();
    //     $this->assertSame(400, $result->getCode());
    //     $this->assertStringContainsString('The request was bad.', $result->getContent());
    // }

    // public function testBadCommandOption()
    // {
    // }
}
