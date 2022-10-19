<?php
namespace Otto\Sapi\Http\Responder;

use FakeProject\App\Payload;

use Otto\Sapi\Http\ActionFactory;
use FakeProject\Sapi\Http\Action\Get;
use FakeProject\Sapi\Http\Action\Post;
use FakeProject\Sapi\Http\Action\Put;
use Otto\Sapi\Http\Responder\Exception;

class ActionResponderTest extends \Otto\TestCase
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
        $responder = $this->container->new(ActionResponder::CLASS);
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
        $responder = $this->container->new(ActionResponder::CLASS);
        $response = $responder($action, Payload::found());
        $this->assertSame(200, $response->getCode());
        $this->assertNull($response->getContent());
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
        $responder = $this->container->new(ActionResponder::CLASS);
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

        $responder = $this->container->new(ActionResponder::CLASS);
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

    public function testActionWithoutPayload()
    {
        $action = $this->container->new(Post::CLASS);

        $responder = $this->container->new(ActionResponder::CLASS);
        $response = $responder($action);
        $this->assertStringContainsString(
            "<p>POST template regardless of payload status.</p>",
            $response->getContent()
        );
    }

    public function testActionWithoutPayload_viewNotFound()
    {
        $action = $this->container->new(Put::CLASS);
        $responder = $this->container->new(ActionResponder::CLASS);

        try {
            $response = $responder($action);
            $this->assertTrue(false, 'should have thrown an exception');
        } catch (Exception\ViewNotFound $e) {
            $this->assertSame(['action:Put-', 'action:Put', 'status:'], $e->views);
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
            [Payload::unauthorized(), 403, 'UNAUTHORIZED'],
            [Payload::updated(), 303, 'UPDATED'],
        ];
    }
}
