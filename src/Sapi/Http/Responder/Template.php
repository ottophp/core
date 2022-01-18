<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder;

use AutoRoute\Route;
use Sapien\Request;
use Sapien\Response;
use Otto\Sapi\Template as SapiTemplate;

class Template extends SapiTemplate
{
    private ?Request $request = null;

    private ?Response $response = null;

    private ?Route $route = null;

    public function request(Request $request = null) : ?Request
    {
        if ($request !== null) {
            $this->request = $request;
        }

        return $this->request;
    }

    public function response(Response $response = null) : ?Response
    {
        if ($response !== null) {
            $this->response = $response;
        }

        return $this->response;
    }

    public function route(Route $route = null) : ?Route
    {
        if ($route !== null) {
            $this->route = $route;
        }

        return $this->route;
    }

    public function fileResponse(
        string|SplFileObject $file,
        string $disposition = 'attachment',
        string $name = null,
        string $type = 'application/octet-stream',
        string $encoding = 'binary',
    ) : Response\FileResponse
    {
        $fileResponse = new Response\FileResponse();
        $fileResponse->setVersion($this->response->getVersion());
        $fileResponse->setCode($this->response->getCode());
        $fileResponse->setHeaders($this->response->getHeaders());
        $fileResponse->setCookies($this->response->getCookies());
        $fileResponse->setHeaderCallbacks($this->response->getHeaderCallbacks());
        $fileResponse->setFile(
            $file,
            $disposition,
            $name,
            $type,
            $encoding
        );

        $this->response($fileResponse);
        return $fileResponse;
    }
}
