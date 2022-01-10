<?php
declare(strict_types=1);

namespace Otto\Domain;

use Capsule\Di\Definitions;
use Capsule\Di\Provider;

class DomainProvider implements Provider
{
    public function provide(Definitions $def) : void
    {
        $def->{App\Action\CreateAction::CLASS}
            ->arguments([
                'directory' => $def->{'otto.directory'},
                'namespace' => $def->{'otto.namespace'}
            ]);
    }
}
