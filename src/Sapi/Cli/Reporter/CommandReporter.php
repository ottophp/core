<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli\Reporter;

use PayloadInterop\DomainPayload;
use Otto\Sapi\Cli\Reporter;
use Otto\Sapi\Cli\Result;

class CommandReporter extends Reporter
{
    protected object $command;

    protected ?DomainPayload $payload = null;

    protected ?string $status = null;

    public function __invoke(
        object $command,
        DomainPayload $payload = null,
    ) : Result
    {
        $this->command = $command;
        $this->setPayload($payload);
        $this->setStatus();
        $method = "report{$this->status}";

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
        $parts = array_slice(explode('\\', get_class($this->command)), 4);
        $name = implode(DIRECTORY_SEPARATOR, $parts);
        $views = [
            "command:{$name}-{$this->status}",
            "command:{$name}",
            "status:{$this->status}",
        ];

        $templateLocator = $this->template->getTemplateLocator();

        foreach ($views as $view) {
            if ($templateLocator->has($view)) {
                return $view;
            }
        }

        return null;

        return $this->strategy->viewNotFound(
            $templateLocator->getPaths(),
            $views
        );
    }

    protected function report() : Result
    {
        return $this->render();
    }

    protected function reportAccepted() : Result
    {
        return $this->render(Result::SUCCESS);
    }

    protected function reportCreated() : Result
    {
        return $this->render(Result::SUCCESS);
    }

    protected function reportDeleted() : Result
    {
        return $this->render(Result::SUCCESS);
    }

    protected function reportError() : Result
    {
        return $this->render(Result::FAILURE);
    }

    protected function reportFound() : Result
    {
        return $this->render(Result::SUCCESS);
    }

    protected function reportInvalid() : Result
    {
        return $this->render(Result::DATAERR);
    }

    protected function reportNotFound() : Result
    {
        return $this->render(Result::FAILURE);
    }

    protected function reportProcessing() : Result
    {
        return $this->render(Result::SUCCESS);
    }

    protected function reportSuccess() : Result
    {
        return $this->render(Result::SUCCESS);
    }

    protected function reportUnauthorized() : Result
    {
        return $this->render(Result::NOPERM);
    }

    protected function reportUpdated() : Result
    {
        return $this->render(Result::SUCCESS);
    }
}
