<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

use AutoRoute;
use Capsule\Di\Definitions;
use Capsule\Di\Provider;
use Otto\Domain\App\Action\CreateAction;

class CliProvider implements Provider
{
    public function provide(Definitions $def) : void
    {
        $def->{CreateAction::CLASS}
            ->arguments([
                'directory' => $def->{'otto.directory'},
                'namespace' => $def->{'otto.namespace'}
            ]);
    }
}
