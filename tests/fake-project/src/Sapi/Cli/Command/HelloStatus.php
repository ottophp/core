<?php
namespace FakeProject\Sapi\Cli\Command;

use Otto\Sapi\Cli\Result;
use Otto\Sapi\Cli\Reporter\CommandReporter;
use Otto\Domain\Payload;

class HelloStatus
{
    public function __construct(
        protected CommandReporter $reporter
    ) {
    }

    public function __invoke(array $options, string $status) : Result
    {
        $payload = new Payload(status: $status);
        return ($this->reporter)($this, $payload);
    }
}
