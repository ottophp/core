<?php

/*
consider naming the bin for the $PROJECT, `otto.php`.
it can be used as the namespace.

e.g. `php ./bin/otto action:create GET /url/path/etc`

the $PROJECT file needs only

   require dirname(__DIR__) . '/vendor/otto/core/bin/otto';

or some such.

then the question is, how do we collect help across *all* available
command sets?

    $ ./bin/otto help-all # for all projects
    $ ./bin/{$PROJECT} help {?$COMMAND}

as vs:

    $ php ./bin/console.php help # for all projects, where help is a special command
    $ php ./bin/console.php help {$PROJECT} # for all projects, where help is a special command
    $ php ./bin/console.php help {$PROJECT} {$COMMAND} # for all projects, where help is a special command
    $ php ./bin/console.php otto {$COMMAND}

*/

use Capsule\Di\Container;
use Capsule\Di\Definitions;

$directory = dirname(__DIR__, 3);
require "{$directory}/vendor/autoload.php";

$container = new Container(
    new Definitions(),
    require "{$directory}/config/cli/providers.php"
);

$console = $container->get(Console::CLASS);

$input = $_SERVER['argv'];

// strip off any leading `php` argument, then:

$result = $console($input);
$code = $result->print();
exit($code);
