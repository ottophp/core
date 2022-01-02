<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder;

use PayloadInterop\DomainPayload;
use Sapien\Request;
use Sapien\Response;
use Otto\Sapi\Http\Responder;

class ActionResponder extends Responder
{
    protected object $action;

    protected ?DomainPayload $payload = null;

    protected ?string $status = null;

    public function __invoke(
        object $action,
        DomainPayload $payload = null,
    ) : Response
    {
        $this->action = $action;
        $this->setPayload($payload);
        $this->setStatus();
        $method = "respond{$this->status}";

        if (! method_exists($this, $method)) {
            throw Exception\MethodNotFound::new(get_class($this), $method);
        }

        return $this->$method();
    }

    protected function setPayload(?DomainPayload $payload) : void
    {
        if ($payload === null) {
            return;
        }

        $this->payload = $payload;
        $this->template->addData($this->payload->getResult());
        $this->template->payload($this->payload);

    }

    protected function setStatus() : void
    {
        if ($this->payload === null) {
            return;
        }

        $status = $this->payload->getStatus();
        $status = ucwords(str_replace('_', ' ', strtolower($status)));
        $this->status = str_replace(' ', '', $status);
    }

    protected function getView() : ?string
    {
        $parts = array_slice(explode('\\', get_class($this->action)), 4);
        $name = implode(DIRECTORY_SEPARATOR, $parts);
        $views = [
            "action:{$name}-{$this->status}",
            "action:{$name}",
            "status:{$this->status}",
        ];

        $templateLocator = $this->template->getTemplateLocator();

        foreach ($views as $view) {
            if ($templateLocator->has($view)) {
                return $view;
            }
        }

        return $this->strategy->viewNotFound(
            $templateLocator->getPaths(),
            $views
        );
    }

    protected function respond() : Response
    {
        return $this->render();
    }

    protected function respondAccepted() : Response
    {
        return $this->render(202);
    }

    protected function respondCreated() : Response
    {
        return $this->render(201);
    }

    protected function respondDeleted() : Response
    {
        return $this->render(200);
    }

    protected function respondError() : Response
    {
        return $this->render(500);
    }

    protected function respondFound() : Response
    {
        return $this->render(200);
    }

    protected function respondInvalid() : Response
    {
        return $this->render(422);
    }

    protected function respondNotFound() : Response
    {
        return $this->render(404);
    }

    protected function respondProcessing() : Response
    {
        return $this->render(102);
    }

    protected function respondSuccess() : Response
    {
        return $this->render(200);
    }

    protected function respondUnauthorized() : Response
    {
        return $this->render(400);
    }

    protected function respondUpdated() : Response
    {
        return $this->render(303);
    }
}
