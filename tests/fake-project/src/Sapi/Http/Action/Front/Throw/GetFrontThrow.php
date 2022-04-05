<?php
namespace FakeProject\Sapi\Http\Action\Front\Throw;

use LogicException;
use Otto\Sapi\Http\Responder;
use Sapien\Request;
use Sapien\Response;

class GetFrontThrow
{
    public function __construct(
        protected Request $request,
        protected Responder $responder
    ) {
    }

    public function __invoke(string $status = null) : Response
    {
        throw new LogicException("Fake logic exception thrown.");
    }
}
