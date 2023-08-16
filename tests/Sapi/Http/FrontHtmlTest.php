<?php
namespace Otto\Sapi\Http;

use FakeProject\App\Payload;

class FrontHtmlTest extends \Otto\TestCase
{
    protected function getFront() : Front
    {
        /** @var Front */
        return $this->container->get(Front::class);
    }

    public function testRoute404() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/none-such';

        $front = $this->getFront();
        $this->assertResponse($front(), 404, 'Route not found for URL.');
    }

    public function testRouteBadRequest() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/front/route/not-an-int';
        $front = $this->getFront();
        $response = $front();
        $this->assertResponse($response, 400, 'The request was bad.');
    }

    public function testRouteMethodNotAllowed() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/front/route';
        $front = $this->getFront();
        $response = $front();
        $this->assertResponse($response, 405, 'The HTTP request method <code>POST</code> was not allowed.');
    }

    public function testRouteCors() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        $_SERVER['REQUEST_URI'] = '/front/route/';
        $front = $this->getFront();
        $response = $front();
        $this->assertResponse($response, 204, null);
    }

    /**
     * @dataProvider provideStatus
     */
    public function testStatus(string $status, int $code) : void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = "/front/status/{$status}";
        $front = $this->getFront();
        $response = $front();
        $this->assertResponse($response, $code, "<p>The domain payload reported a status of {$status}.</p>");
    }

    /**
     * @return array<array{string, int}>
     */
    public static function provideStatus() : array
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

    public function testThrowable() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = "/front/throw";
        $front = $this->getFront();
        $response = $front();
        $this->assertResponse($response, 500, "<p>Logic Exception</p>");
        $this->assertResponse($response, 500, "Fake logic exception thrown.");
    }

    public function testAction() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = "/";
        $_POST['_method'] = 'PATCH';
        $front = $this->getFront();
        $response = $front();
        $this->assertResponse($response, 200, "<p>PATCH status was SUCCESS.</p>");
    }
}
