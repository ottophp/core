<?php
namespace FakeProject\Sapi\Http\Action;

use Otto\Sapi\Http\Responder;
use Sapien\Request;
use Sapien\Response;
use FakeProject\Domain\Payload;

class Patch
{
    public function __construct(
        protected Request $request,
        protected Responder $responder
    ) {
    }

    public function __invoke(string $status = null)
    {
        $status ??= Payload::SUCCESS;
        return ($this->responder)($this, new Payload($status));
    }
}
