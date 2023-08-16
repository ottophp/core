<?php
namespace Otto\Sapi\Http\Responder;

use FakeProject\App\Payload;

use Otto\Sapi\Http\ActionFactory;
use FakeProject\Sapi\Http\Action\Get;
use FakeProject\Sapi\Http\Action\Post;
use FakeProject\Sapi\Http\Action\Put;
use Otto\Sapi\Http\Responder\Exception;
use Sapien\Response;

class ActionResponderTest extends \Otto\TestCase
{
    protected function getResponse(string $actionClass, ?Payload $payload) : Response
    {
        /** @var ActionResponder */
        $responder = $this->container->new(ActionResponder::class);
        /** @var object */
        $action = $this->container->new($actionClass);
        return $responder($action, $payload);
    }

    /**
     * @dataProvider providePayload
     */
    public function testStatusOnly(
        Payload $payload,
        int $expectCode,
        string $expectText
    ) : void
    {
        $response = $this->getResponse(Get::class, $payload);
        $this->assertResponse(
            $response,
            $expectCode,
            "<p>The domain payload reported a status of {$expectText}.</p>",
        );
    }

    public function testHeadContentIsNull() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $response = $this->getResponse(Get::class, Payload::found());
        $this->assertResponse($response, 200, null);
    }

    /**
     * @dataProvider providePayload
     */
    public function testActionOnly(
        Payload $payload,
        int $expectCode,
        string $expectText
    ) : void
    {
        $response = $this->getResponse(Post::class, $payload);
        $this->assertResponse($response, $expectCode, "<p>POST template regardless of payload status.</p>");
    }

    /**
     * @dataProvider providePayload
     */
    public function testActionAndStatus(
        Payload $payload,
        int $expectCode,
        string $expectText
    ) : void
    {
        // if ($expectText === 'INVALID') {
        //     $expectText = '<p>PATCH data was not valid.</p>';
        // } else {
        //     $expectText = "<p>PATCH status was {$expectText}.</p>";
        // }

        $response = $this->getResponse(Post::class, $payload);
        $this->assertResponse($response, $expectCode, "<p>POST template regardless of payload status.</p>");
    }

    public function testActionWithoutPayload() : void
    {
        $response = $this->getResponse(Post::class, null);
        $this->assertResponse($response, null, "<p>POST template regardless of payload status.</p>");
    }

    public function testActionWithoutPayload_viewNotFound() : void
    {
        try {
            $response = $this->getResponse(Put::class, null);
            $this->assertTrue(false, 'should have thrown an exception');
        } catch (Exception\ViewNotFound $e) {
            $this->assertSame(['action:Put-', 'action:Put', 'status:'], $e->views);
        }
    }

    /**
     * @return array<int, array{Payload, int, string}>
     */
    public static function providePayload() : array
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
