<?php
namespace Otto;

use Capsule\Di\Container;
use Capsule\Di\Definitions;
use Otto\OttoProvider;
use Otto\Sapi\Http\HttpProvider;
use Otto\Sapi\Http\Responder\Helper\Rot13;
use Sapien\Response;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Container $container;

    protected string $format = 'html';

    protected function setUp() : void
    {
        $this->container = new Container(new Definitions(), [
            new OttoProvider(
                directory: __DIR__ . DIRECTORY_SEPARATOR . 'fake-project',
                namespace: 'FakeProject',
            ),
            new HttpProvider(
                format: $this->format,
            ),
        ]);
    }

    protected function assertResponse(Response $response, ?int $expectCode, ?string $expectText) : void
    {
        $this->assertSame($expectCode, $response->getCode());

        if ($expectText === null) {
            $this->assertEmpty($response->getContent());
        } else {
            /** @var string */
            $actual = $response->getContent();
            $this->assertStringContainsString($expectText, $actual);
        }
    }
}
