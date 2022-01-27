<?php
namespace FakeProject\Sapi\Cli\Command;

use Otto\Sapi\Cli\Result;
use Otto\Sapi\Cli\Reporter\CommandReporter;
use LogicException;

class Pitch
{
    public function __construct(
        protected CommandReporter $reporter
    ) {
    }

    public function __invoke(array $options) : Result
    {
        throw new LogicException("Fake logic exception thrown.");
    }
}
