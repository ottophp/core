<?php
namespace FakeProject\Sapi\Http\Action;

use Otto\Sapi\Http\Responder\ActionResponder;
use Sapien\Request;
use Sapien\Response;

class Put
{
    public function __construct(
        protected Request $request,
        protected ActionResponder $responder
    ) {
    }

    public function __invoke()
    {
        return ($this->responder)($this);
    }
}
