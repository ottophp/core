<?php
namespace Otto\Sapi\Http\Responder;

use Error;
use Exception;
use LengthException;
use Sapien\Response;
use Throwable;

class FrontResponderTest extends \Otto\TestCase
{
    protected function getResponse(Throwable $e) : Response
    {
        /** @var FrontResponder */
        $frontResponder = $this->container->get(FrontResponder::class);
        return $frontResponder($e);
    }

    public function testException() : void
    {
        $e = new Exception('fake exception', previous: new Exception('previous exception'));
        $response = $this->getResponse($e);

        $this->assertResponse($response, 500, "<p>Exception</p>");
        $this->assertResponse($response, 500, "Exception: fake exception");
    }

    public function testError() : void
    {
        $e = new Error('fake error');
        $response = $this->getResponse($e);

        $this->assertResponse($response, 500, "<p>Error</p>");
        $this->assertResponse($response, 500, "Error: fake error");
    }

    public function testHierarchy() : void
    {
        $e = new LengthException('fake length');
        $response = $this->getResponse($e);

        $this->assertResponse($response, 500, "<p>Logic Exception</p>");
        $this->assertResponse($response, 500, "LengthException: fake length");
    }
}
