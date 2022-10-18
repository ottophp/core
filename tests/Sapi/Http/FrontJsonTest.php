<?php
namespace Otto\Sapi\Http;

use FakeProject\App\Payload;
use AutoRoute\Exception\NotFound;
use AutoRoute\Exception\InvalidArgument;
use AutoRoute\Exception\MethodNotAllowed;
use AutoRoute\Route;
use LogicException;

class FrontJsonTest extends \Otto\TestCase
{
    protected $format = 'json';

    public function testRoute404()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/none-such';
        $front = $this->container->get(Front::CLASS);
        $response = $front();
        $this->assertSame(404, $response->getCode());
        $actual = $response->getContent();
        $this->assertSame(NotFound::CLASS, $actual->e->__CLASS__);
        $this->assertInstanceOf(Route::CLASS, $actual->route);
    }

    public function testRouteBadRequest()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/front/route/not-an-int';
        $front = $this->container->get(Front::CLASS);
        $response = $front();
        $this->assertSame(400, $response->getCode());
        $actual = $response->getContent();
        $this->assertSame(InvalidArgument::CLASS, $actual->e->__CLASS__);
        $this->assertInstanceOf(Route::CLASS, $actual->route);
    }

    public function testRouteMethodNotAllowed()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/front/route';
        $front = $this->container->get(Front::CLASS);
        $response = $front();
        $this->assertSame(405, $response->getCode());
        $actual = $response->getContent();
        $this->assertSame(MethodNotAllowed::CLASS, $actual->e->__CLASS__);
        $this->assertInstanceOf(Route::CLASS, $actual->route);
    }

    public function testRouteCors()
    {
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        $_SERVER['REQUEST_URI'] = '/front/route/';
        $front = $this->container->get(Front::CLASS);
        $response = $front();
        $this->assertSame(204, $response->getCode());
        $actual = $response->getContent();
        $this->assertFalse(isset($actual->e));
        $this->assertFalse(isset($actual->route));
    }

    /**
     * @dataProvider provideStatus
     */
    public function testStatus(string $status, int $code)
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = "/front/status/{$status}";
        $front = $this->container->get(Front::CLASS);
        $response = $front();
        $this->assertSame($code, $response->getCode());
        $actual = $response->getContent();
        $this->assertSame($status, $actual->_status);
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
        $front = $this->container->get(Front::CLASS);
        $response = $front();
        $this->assertSame(500, $response->getCode());
        $actual = $response->getContent();
        $this->assertSame(LogicException::CLASS, $actual->e->__CLASS__);
        $this->assertSame("Fake logic exception thrown.", $actual->e->message);
    }

    public function testAction()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = "/";
        $_POST['_method'] = 'PATCH';
        $front = $this->container->get(Front::CLASS);
        $response = $front();
        $this->assertSame(200, $response->getCode());
        $actual = $response->getContent();
        $this->assertSame(Payload::SUCCESS, $actual->_status);
    }
}
