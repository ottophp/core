<?php
declare(strict_types=1);

namespace Otto;

use AutoRoute;
use Capsule\Di\Definitions;
use Capsule\Di\Provider;
use Otto\Sapi\Http\Responder;
use Otto\Sapi\Http\Responder\Strategy;
use Otto\Sapi\Http\Responder\Template;
use Qiq;
use Sapien\Request;

class OttoProvider implements Provider
{
    public function __construct(
        public readonly string $directory,
        public readonly string $namespace,
    ) {
    }

    public function provide(Definitions $def) : void
    {
        $def->{'otto.directory'} = $this->directory;
        $def->{'otto.namespace'} = $this->namespace;
    }
}
