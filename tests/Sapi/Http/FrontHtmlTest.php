<?php
namespace Otto\Sapi\Http;

use FakeProject\App\Payload;

class FrontHtmlTest extends \Otto\TestCase
{
    public function testRoute404()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/none-such';
        $front = $this->container->get(Front::class);
        $response = $front();
        $this->assertSame(404, $response->getCode());
        $this->assertStringContainsString('Route not found for URL.', $response->getContent());
    }

    public function testRouteBadRequest()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/front/route/not-an-int';
        $front = $this->container->get(Front::class);
        $response = $front();
        $this->assertSame(400, $response->getCode());
        $this->assertStringContainsString('The request was bad.', $response->getContent());
    }

    public function testRouteMethodNotAllowed()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/front/route';
        $front = $this->container->get(Front::class);
        $response = $front();
        $this->assertSame(405, $response->getCode());
        $this->assertStringContainsString('The HTTP request method <code>POST</code> was not allowed.', $response->getContent());
    }

    public function testRouteCors()
    {
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        $_SERVER['REQUEST_URI'] = '/front/route/';
        $front = $this->container->get(Front::class);
        $response = $front();
        $this->assertSame(204, $response->getCode());
        $this->assertEmpty($response->getContent());
    }

    /**
     * @dataProvider provideStatus
     */
    public function testStatus(string $status, int $code)
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = "/front/status/{$status}";
        $front = $this->container->get(Front::class);
        $response = $front();
        $this->assertSame($code, $response->getCode());
        $this->assertStringContainsString(
            "<p>The domain payload reported a status of {$status}.</p>",
            $response->getContent()
        );
    }

    public function provideStatus() : array
    {
        return [
            [Payload::ACCEPTED, 202],
            [Payload::CREATED, 201],
            [Payload::DELETED, 200],
            [Payload::ERROR, 500],
            [Payload::FOUND, 200],
            [Payload::INVALID, 422],
            [Payload::NOT_FOUND, 404],
            [Payload::PROCESSING, 102],
            [Payload::SUCCESS, 200],
            [Payload::UNAUTHORIZED, 403],
            [Payload::UPDATED, 303],
        ];
    }

    public function testThrowable()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = "/front/throw";
        $front = $this->container->get(Front::class);
        $response = $front();
        $this->assertSame(500, $response->getCode());
        $this->assertStringContainsString(
            "<p>Logic Exception</p>",
            $response->getContent()
        );
        $this->assertStringContainsString(
            "Fake logic exception thrown.",
            $response->getContent()
        );
    }

    public function testAction()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = "/";
        $_POST['_method'] = 'PATCH';
        $front = $this->container->get(Front::class);
        $response = $front();
        $this->assertSame(200, $response->getCode());
        $this->assertStringContainsString(
            "<p>PATCH status was SUCCESS.</p>",
            $response->getContent()
        );
    }
}
