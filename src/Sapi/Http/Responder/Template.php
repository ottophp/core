<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder;

use AutoRoute\Route;
use JsonSerializable;
use PayloadInterop\DomainPayload;
use Qiq\HelperLocator;
use Qiq\Template as QiqTemplate;
use Qiq\TemplateLocator;
use ReflectionClass;
use Sapien\Request;
use Sapien\Response;
use SplFileObject;
use Throwable;

class Template extends QiqTemplate
{
    private ?DomainPayload $payload = null;

    private Response $response;

    private ?Route $route = null;

    public function __construct(
        TemplateLocator $templateLocator,
        HelperLocator $helperLocator,
        private Request $request
    ) {
        parent::__construct($templateLocator, $helperLocator);
    }

    public function request() : Request
    {
        return $this->request;
    }

    public function response(Response $response = null) : Response
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

    public function payload(DomainPayload $payload = null) : ?DomainPayload
    {
        if ($payload !== null) {
            $this->payload = $payload;
        }

        return $this->payload;
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

    public function decomposeException(Throwable $e) : object
    {
        $vars['__CLASS__'] = get_class($e);
        $vars['__STRING__'] = (string) $e;

        $rc = new ReflectionClass($e);

        foreach ($rc->getProperties() as $rp) {
            $rp->setAccessible(true);
            $vars[$rp->getName()] = $rp->getValue($e);
        }

        $vars['trace'] = $e->getTraceAsString();
        $vars['previous'] ??= null;

        if ($vars['previous'] instanceof Throwable) {
            $vars['previous'] = $this->decomposeException($vars['previous']);
        }

        return new class($vars) implements JsonSerializable {
            public function __construct(protected readonly array $vars)
            {
            }
            public function __get(string $key) : mixed
            {
                return $this->vars[$key];
            }
            public function __toString() : string
            {
                return $this->vars['__STRING__'];
            }
            public function jsonSerialize() : mixed
            {
                return $this->vars;
            }
        };
    }
}
