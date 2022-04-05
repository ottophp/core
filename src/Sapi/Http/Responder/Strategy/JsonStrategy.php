<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder\Strategy;

use Otto\Sapi\Http\Responder\Strategy;
use Sapien\Response;

class JsonStrategy extends Strategy
{
    public function getPaths() : array
    {
        return [
            "{$this->directory}/resources/responder/json/view",
            "action:{$this->directory}/resources/responder/json/action",
            "layout:{$this->directory}/resources/responder/json/layout",
            "status:{$this->directory}/resources/responder/json/status",
            "throwable:{$this->directory}/resources/responder/json/throwable",
            "layout:{$this->directory}/vendor/ottophp/core/resources/responder/json/layout",
            "status:{$this->directory}/vendor/ottophp/core/resources/responder/json/status",
            "throwable:{$this->directory}/vendor/ottophp/core/resources/responder/json/throwable",
        ];
    }

    public function newResponse() : Response
    {
        $response = new Response\JsonResponse();
        $response->setHeader('content-type', 'application/json');
        $response->setJsonFlags(
            JSON_PRETTY_PRINT
            | JSON_INVALID_UTF8_SUBSTITUTE
            | JSON_PRESERVE_ZERO_FRACTION
            | JSON_THROW_ON_ERROR
            | JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
        );
        return $response;
    }

    public function viewNotFound(
        array $paths,
        array $views
    ) : ?string
    {
        return null;
    }
}
