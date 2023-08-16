<?php
declare(strict_types=1);

namespace Otto;

use Capsule\Di\Definitions;
use Capsule\Di\Provider;
use Qiq;

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
