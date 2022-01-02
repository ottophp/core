<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder;

use Sapien\Request;
use Sapien\Response;

abstract class Strategy
{
    public function __construct(
        protected Request $request,
        protected string $directory,
        protected ?string $layout,
    ) {
    }

    public function getLayout() : ?string
    {
        return $this->layout;
    }

    abstract public function viewNotFound(
        array $paths,
        array $views
    ) : ?string;

    abstract public function getPaths() : array;

    abstract public function newResponse() : Response;
}
