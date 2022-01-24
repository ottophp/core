<?php
declare(strict_types=1);

namespace FakeProject\Sapi\Cli;

use Capsule\Di\Definitions;
use Capsule\Di\Provider;
use Otto\Sapi\Cli\Reporter;

class CliProvider implements Provider
{
    public function __construct()
    {
    }

    public function provide(Definitions $def) : void
    {
        $def->{Reporter\Strategy::CLASS}
            ->refArgument('namespaces')[__NAMESPACE__] = __DIR__;
    }
}
