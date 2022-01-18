<?php
namespace Otto\Sapi\Http;

use Capsule\Di\Container;
use Capsule\Di\Definitions;
use Otto\OttoProvider;
use Otto\Sapi\SapiProvider;
use Otto\Sapi\Http\HttpProvider;
use Otto\Sapi\Http\Responder\Helper\Rot13;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected $format = 'html';

    protected function setUp() : void
    {
        $this->container = new Container(new Definitions(), [
            new OttoProvider(
                directory: dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'fake-project',
                namespace: 'FakeProject',
            ),
            new SapiProvider(
                helpers: [
                    'rot13' => Rot13::CLASS,
                ],
            ),
            new HttpProvider(
                format: $this->format,
            ),
        ]);
    }
}
