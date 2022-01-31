<?php
namespace FakeProject\Sapi\Cli\Command;

use Otto\Sapi\Cli\Options;
use Otto\Sapi\Cli\Result;
use Otto\Sapi\Cli\Reporter\CommandReporter;
use FakeProject\Domain\Payload;

class HelloStatus
{
    public function __construct(
        protected CommandReporter $reporter
    ) {
    }

    public function __invoke(Options $options, string $status) : Result
    {
        $payload = new Payload(status: $status);
        return ($this->reporter)($this, $payload);
    }
}
