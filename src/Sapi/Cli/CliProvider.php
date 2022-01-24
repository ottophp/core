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
        $def->{Reporter\Strategy::CLASS}
            ->arguments([
                'directory' => $def->{'otto.directory'},
                'layout' => 'layout:main',
            ]);

        /*
        how can i make this as easy as possible?
        $PROJECT\Sapi\Cli => /path/to/vendor/package/src/Sapi/Cli

        what to call it, then? namespaces?

        then the Strategy class has to look at the class of object,
        see if it fits in that namespace, then manipulate the dir
        to get to the resources for that namespace.

        this precludes the idea of more than one package of CLI tools
        in the same namespace.
        */
        $def->{Reporter\Strategy::CLASS}
            ->refArgument('namespaces')[__NAMESPACE__] = __DIR__;
    }
}
