<?php
namespace Otto\Sapi\Http\Responder;

use FakeProject\Domain\Payload;
use Sapien\Request;
use Sapien\Response;

class TemplateTest extends \Otto\TestCase
{
    public function test()
    {
        $templateFactory = $this->container->get(Template\ResponderTemplateFactory::CLASS);
        $template = $templateFactory($this->container);
        $payload = Payload::success();
        $template->payload()->set($payload);
        $this->assertSame($payload, $template->payload()->get());
    }
}
