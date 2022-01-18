<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

use Capsule\Di\Definitions;
use Capsule\Di\Provider;

class CliProvider implements Provider
{
    public function __construct()
    {
    }

    public function provide(Definitions $def) : void
    {
        $directory = $def->{'otto.directory'};
        $def->{Reporter::CLASS}
            ->arguments([
                'directory' => $def->{'otto.directory'},
            ]
        );
    }
}
