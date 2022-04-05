<?php
namespace Otto\Sapi\Http;

use FakeProject\Domain\Payload;

use Otto\Sapi\Http\ActionFactory;
use Otto\Sapi\Http\TestCase;
use FakeProject\Sapi\Http\Action\Get;
use FakeProject\Sapi\Http\Action\Post;
use FakeProject\Sapi\Http\Action\Put;
use Otto\Sapi\Http\Responder\Exception;
use stdClass;
use LengthException;
use LogicException;
use Error;

class ResponderTest extends TestCase
{
    /**
     * @dataProvider providePayload
     */
    public function testStatusOnly(
        Payload $payload,
        int $expectCode,
        string $expectText
    ) : void
    {
        $action = $this->container->new(Get::CLASS);
        $responder = $this->container->new(Responder::CLASS);
        $response = $responder($action, $payload);
        $this->assertSame($expectCode, $response->getCode());
        $this->assertStringContainsString(
            "<p>The domain payload reported a status of {$expectText}.</p>",
            $response->getContent()
        );
    }

    public function testHeadContentIsNull()
    {
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $action = $this->container->new(Get::CLASS);
        $responder = $this->container->new(Responder::CLASS);
        $response = $responder($action, Payload::found());
        $this->assertSame(200, $response->getCode());
        $this->assertNull($response->getContent());
    }

    public function testStatus_methodNotFound()
    {
        $action = $this->container->new(Get::CLASS);
        $payload = Payload::nonesuch();
        $responder = $this->container->new(Responder::CLASS);

        try {
            $response = $responder($action, $payload);
            $this->assertTrue(false, 'should have thrown an exception');
        } catch (Exception\MethodNotFound $actual) {
            $expect = [
                'class' => Responder::CLASS,
                'method' => 'respondNonesuch',
            ];
            $this->assertEquals($expect, $actual->getInfo());
        }
    }

    /**
     * @dataProvider providePayload
     */
    public function testActionOnly(
        Payload $payload,
        int $expectCode,
        string $expectText
    ) {
        $action = $this->container->new(Post::CLASS);
        $responder = $this->container->new(Responder::CLASS);
        $response = $responder($action, $payload);
        $this->assertSame($expectCode, $response->getCode());
        $this->assertStringContainsString(
            "<p>POST template regardless of payload status.</p>",
            $response->getContent()
        );
    }

    /**
     * @dataProvider providePayload
     */
    public function testActionAndStatus(
        Payload $payload,
        int $expectCode,
        string $expectText
    ) {
        $action = $this->container->new(Post::CLASS);

        $responder = $this->container->new(Responder::CLASS);
        $response = $responder($action, $payload);
        $this->assertSame($expectCode, $response->getCode());

        if ($expectText === 'INVALID') {
            $expectText = '<p>PATCH data was not valid.</p>';
        } else {
            $expectText = "<p>PATCH status was {$expectText}.</p>";
        }

        $this->assertStringContainsString(
            "<p>POST template regardless of payload status.</p>",
            $response->getContent()
        );
    }

    public function testActionAndStatus_methodNotFound()
    {
        $action = $this->container->new(Post::CLASS);

        $responder = $this->container->new(Responder::CLASS);
        $payload = Payload::nonesuch();

        try {
            $response = $responder($action, $payload);
            $this->assertTrue(false, 'should have thrown an exception');
        } catch (Exception\MethodNotFound $actual) {
            $expect = [
                'class' => Responder::CLASS,
                'method' => 'respondNonesuch',
            ];
            $this->assertEquals($expect, $actual->getInfo());
        }
    }

    public function testActionWithoutPayload()
    {
        $action = $this->container->new(Post::CLASS);

        $responder = $this->container->new(Responder::CLASS);
        $response = $responder($action);
        $this->assertStringContainsString(
            "<p>POST template regardless of payload status.</p>",
            $response->getContent()
        );
    }

    public function testActionWithoutPayload_viewNotFound()
    {
        $action = $this->container->new(Put::CLASS);
        $responder = $this->container->new(Responder::CLASS);

        try {
            $response = $responder($action);
            $this->assertTrue(false, 'should have thrown an exception');
        } catch (Exception\ViewNotFound $e) {
            $this->assertTrue(true);
            $actual = $e->getInfo();
            $this->assertSame(
                ['action:Put-', 'action:Put', 'status:'],
                $actual['views']
            );
        }
    }

    public function providePayload()
    {
        return [
            [Payload::accepted(), 202, 'ACCEPTED'],
            [Payload::created(), 201, 'CREATED'],
            [Payload::deleted(), 200, 'DELETED'],
            [Payload::error(), 500, 'ERROR'],
            [Payload::found(), 200, 'FOUND'],
            [Payload::invalid(), 422, 'INVALID'],
            [Payload::notFound(), 404, 'NOT_FOUND'],
            [Payload::processing(), 102, 'PROCESSING'],
            [Payload::success(), 200, 'SUCCESS'],
            [Payload::unauthorized(), 400, 'UNAUTHORIZED'],
            [Payload::updated(), 303, 'UPDATED'],
        ];
    }

    public function testThrowableException()
    {
        $responder = $this->container->new(Responder::CLASS);

        $e = new Exception('fake exception', previous: new Exception('previous exception'));
        $response = $responder(new stdClass(), $e);

        $this->assertSame(500, $response->getCode());
        $this->assertStringContainsString("<p>Exception</p>", $response->getContent());
        $this->assertStringContainsString("Exception: fake exception", $response->getContent());
    }

    public function testThrowableError()
    {
        $responder = $this->container->new(Responder::CLASS);

        $e = new Error('fake error');
        $response = $responder(new stdClass(), $e);

        $this->assertSame(500, $response->getCode());
        $this->assertStringContainsString("<p>Error</p>", $response->getContent());
        $this->assertStringContainsString("Error: fake error", $response->getContent());
    }

    public function testThrowableHierarchy()
    {
        $responder = $this->container->new(Responder::CLASS);

        $e = new LengthException('fake length');
        $response = $responder(new stdClass(), $e);

        $this->assertSame(500, $response->getCode());
        $this->assertStringContainsString("<p>Logic Exception</p>", $response->getContent());
        $this->assertStringContainsString("LengthException: fake length", $response->getContent());
    }
}
