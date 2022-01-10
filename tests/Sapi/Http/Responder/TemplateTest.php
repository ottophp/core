<?php
namespace Otto\Sapi\Http\Responder;

use FakeProject\Domain\Payload;
use Sapien\Request;
use Sapien\Response;

class TemplateTest extends \Otto\TestCase
{
    public function test()
    {
        $template = $this->container->new(Template::CLASS);

        $request = $this->container->get(Request::CLASS);
        $template->request($request);

        $response = new Response();
        $payload = Payload::success();

        $template->response($response);
        $template->payload($payload);

        $this->assertSame($request, $template->request());
        $this->assertSame($response, $template->response());
        $this->assertSame($payload, $template->payload());

        $template->fileResponse(__DIR__ . DIRECTORY_SEPARATOR . 'fake.txt');
        $this->assertInstanceOf(Response\FileResponse::CLASS, $template->response());
    }
}
