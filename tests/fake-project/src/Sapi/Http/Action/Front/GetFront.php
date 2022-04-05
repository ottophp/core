<?php
namespace FakeProject\Sapi\Http\Action\Front;

use FakeProject\Domain\Payload;
use Otto\Sapi\Http\Responder;
use Sapien\Request;
use Sapien\Response;

class GetFront
{
    public function __construct(
        protected Request $request,
        protected Responder $responder
    ) {
    }

    public function __invoke(string $status = null) : Response
    {
        $payload = new Payload($status);
        return ($this->responder)($this, $payload);
    }
}
