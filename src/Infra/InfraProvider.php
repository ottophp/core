<?php
declare(strict_types=1);

namespace Otto\Infra;

use AutoRoute;
use Capsule\Di\Definitions;
use Capsule\Di\Provider;

class InfraProvider implements Provider
{
    public function provide(Definitions $def) : void
    {
        $this->provideAutoRoute($def);
    }

    protected function provideAutoRoute(Definitions $def) : void
    {
        $def->{AutoRoute\Config::CLASS}
            ->arguments([
                'namespace' => $def->{'otto.namespace'} . '\\Sapi\Http\\Action',
                'directory' => $def->{'otto.directory'} . '/src/Sapi/Http/Action',
            ]);

        $def->{AutoRoute\Router::CLASS}
            ->argument('logger', $def->get(AutoRoute\Logger::CLASS));
    }
}
