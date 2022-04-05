<?php
namespace FakeProject\Sapi\Http\Action\Front\Route;

use Otto\Sapi\Http\Responder;
use Sapien\Request;
use Sapien\Response;

class GetFrontRoute
{
    public function __construct(
        protected Request $request,
        protected Responder $responder
    ) {
    }

    public function __invoke(int $code) : Response
    {
        return ($this->responder)($this);
    }
}
