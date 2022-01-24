<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder\Strategy;

use Otto\Sapi\Http\Responder\Exception\ViewNotFound;
use Otto\Sapi\Http\Responder\Strategy;
use Otto\Sapi\Http\Responder\Template;
use Sapien\Response;

class HtmlStrategy extends Strategy
{
    public function getPaths() : array
    {
        return [
            "{$this->directory}/resources/responder/html/view",
            "action:{$this->directory}/resources/responder/html/action",
            "front:{$this->directory}/resources/responder/html/front",
            "layout:{$this->directory}/resources/responder/html/layout",
            "status:{$this->directory}/resources/responder/html/status",
            "front:{$this->directory}/vendor/ottophp/core/resources/responder/html/front",
            "layout:{$this->directory}/vendor/ottophp/core/resources/responder/html/layout",
            "status:{$this->directory}/vendor/ottophp/core/resources/responder/html/status",
        ];
    }

    public function newResponse() : Response
    {
        $response = new Response();
        $response->setHeader('content-type', 'text/html');
        return $response;
    }

    public function viewNotFound(
        array $paths,
        array $views
    ) : ?string
    {
        throw ViewNotFound::new(
            $paths,
            $views
        );
    }
}
