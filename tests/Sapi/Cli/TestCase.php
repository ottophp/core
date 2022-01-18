<?php
namespace Otto\Sapi\Cli;

use Capsule\Di\Container;
use Capsule\Di\Definitions;
use Otto\OttoProvider;
use Otto\Sapi\Cli\CliProvider;
use Otto\Sapi\SapiProvider;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp() : void
    {
        $this->container = new Container(new Definitions(), [
            new OttoProvider(
                directory: dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'fake-project',
                namespace: 'FakeProject',
            ),
            new SapiProvider(),
            new CliProvider(),
        ]);
    }
}
