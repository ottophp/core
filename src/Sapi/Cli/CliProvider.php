<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

use AutoRoute;
use Capsule\Di\Definitions;
use Capsule\Di\Provider;
use Otto\Sapi\Http\Responder;
use Otto\Sapi\Http\Responder\Strategy;
use Otto\Sapi\Http\Responder\Template;
use Qiq;
use Sapien\Request;

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
