<?php
use Capsule\Di\Container;
use Capsule\Di\Definitions;

$directory = dirname(__DIR__, 3);
require "{$directory}/vendor/autoload.php";

$container = new Container(
    new Definitions(),
    require "{$directory}/config/cli/providers.php"
);

$console = $container->get(Console::CLASS);
$result = $console($_SERVER['argv']);
$code = $result->print();
exit($code);
