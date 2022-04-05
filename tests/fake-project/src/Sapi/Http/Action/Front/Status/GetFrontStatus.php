<?php
namespace FakeProject\Sapi\Http\Action\Front\Status;

use FakeProject\Domain\Payload;
use Otto\Sapi\Http\Responder;
use Sapien\Request;
use Sapien\Response;

class GetFrontStatus
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
