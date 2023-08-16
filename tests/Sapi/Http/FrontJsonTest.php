<?php
namespace Otto\Sapi\Http;

use AutoRoute\Exception\InvalidArgument;
use AutoRoute\Exception\MethodNotAllowed;
use AutoRoute\Exception\NotFound;
use AutoRoute\Route;
use FakeProject\App\Payload;
use LogicException;
use pmjones\ThrowableProperties;
use Throwable;
use Sapien\Response;

class FrontJsonTest extends \Otto\TestCase
{
    protected string $format = 'json';

    protected function getFront() : Front
    {
        /** @var Front */
        return $this->container->get(Front::class);
    }

    /**
     * @return array{e:Throwable, route:Route, _status:string}
     */
    protected function getContent(Response $response) : array
    {
        /** @var array{e:Throwable, route:Route, _status:string} */
        return $response->getContent();
    }

    public function testRoute404() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/none-such';
        $front = $this->getFront();
        $response = $front();
        $this->assertSame(404, $response->getCode());
        $actual = $this->getContent($response);
        $this->assertInstanceof(NotFound::class, $actual['e']);
        $this->assertInstanceOf(Route::class, $actual['route']);
    }

    public function testRouteBadRequest() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/front/route/not-an-int';
        $front = $this->getFront();
        $response = $front();
        $this->assertSame(400, $response->getCode());
        $actual = $this->getContent($response);
        $this->assertInstanceOf(InvalidArgument::class, $actual['e']);
        $this->assertInstanceOf(Route::class, $actual['route']);
    }

    public function testRouteMethodNotAllowed() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/front/route';
        $front = $this->getFront();
        $response = $front();
        $this->assertSame(405, $response->getCode());
        $actual = $this->getContent($response);
        $this->assertInstanceOf(MethodNotAllowed::class, $actual['e']);
        $this->assertInstanceOf(Route::class, $actual['route']);
    }

    public function testRouteCors() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        $_SERVER['REQUEST_URI'] = '/front/route/';
        $front = $this->getFront();
        $response = $front();
        $this->assertSame(204, $response->getCode());
        $actual = $response->getContent();
        $this->assertTrue(is_string($actual) && $actual === '');
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
        $this->assertSame($code, $response->getCode());
        $actual = $this->getContent($response);
        $this->assertSame($status, $actual['_status']);
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
        $this->assertSame(500, $response->getCode());
        $actual = $this->getContent($response);
        $this->assertInstanceOf(ThrowableProperties::class, $actual['e']);
        $this->assertSame(LogicException::class, $actual['e']->class);
        $this->assertSame("Fake logic exception thrown.", $actual['e']->message);
    }

    public function testAction() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = "/";
        $_POST['_method'] = 'PATCH';
        $front = $this->getFront();
        $response = $front();
        $this->assertSame(200, $response->getCode());
        $actual = $this->getContent($response);
        $this->assertSame(Payload::SUCCESS, $actual['_status']);
    }
}
