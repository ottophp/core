<?php
namespace FakeProject\Sapi\Cli\Command;

use Otto\Sapi\Cli\Result;
use Otto\Sapi\Cli\Reporter\CommandReporter;

class FakeCmd
{
    public function __construct(
        protected CommandReporter $reporter
    ) {
    }

    public function __invoke() : Result
    {
        return ($this->reporter)($this);
    }
}
