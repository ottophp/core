<?php
namespace Otto\Sapi\Http\Responder;

use Exception;
use Error;
use LengthException;
use Otto\Sapi\Http\TestCase;

class FrontResponderTest extends TestCase
{
    public function testException()
    {
        $frontResponder = $this->container->get(FrontResponder::CLASS);

        $e = new Exception('fake exception', previous: new Exception('previous exception'));
        $response = $frontResponder($e);

        $this->assertSame(500, $response->getCode());
        $this->assertStringContainsString("<p>Exception</p>", $response->getContent());
        $this->assertStringContainsString("Exception: fake exception", $response->getContent());
    }

    public function testError()
    {
        $frontResponder = $this->container->get(FrontResponder::CLASS);

        $e = new Error('fake error');
        $response = $frontResponder($e);

        $this->assertSame(500, $response->getCode());
        $this->assertStringContainsString("<p>Error</p>", $response->getContent());
        $this->assertStringContainsString("Error: fake error", $response->getContent());
    }

    public function testHierarchy()
    {
        $frontResponder = $this->container->get(FrontResponder::CLASS);

        $e = new LengthException('fake length');
        $response = $frontResponder($e);

        $this->assertSame(500, $response->getCode());
        $this->assertStringContainsString("<p>Logic Exception</p>", $response->getContent());
        $this->assertStringContainsString("LengthException: fake length", $response->getContent());
    }
}
