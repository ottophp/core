<?php
namespace FakeProject\Sapi\Http\Action;

use Otto\Sapi\Http\Responder;
use Sapien\Request;
use Sapien\Response;

class Get
{
    public function __construct(
        protected Request $request,
        protected Responder $responder
    ) {
    }

    public function __invoke() : Response
    {
        return ($this->responder)($this);
    }
}
