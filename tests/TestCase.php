<?php
namespace Otto;

use Capsule\Di\Container;
use Capsule\Di\Definitions;
use Otto\Sapi\Http\HttpProvider;
use Otto\Sapi\Http\Responder\Helper\Rot13;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected $format = 'html';

    protected function setUp() : void
    {
        $this->container = new Container(new Definitions(), [
            new HttpProvider(
                directory: __DIR__ . DIRECTORY_SEPARATOR . 'fake-project',
                namespace: 'FakeProject',
                format: $this->format,
                helpers: [
                    'rot13' => Rot13::CLASS,
                ],
            ),
        ]);
    }
}
