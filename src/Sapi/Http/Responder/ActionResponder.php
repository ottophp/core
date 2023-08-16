<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder;

use Otto\Sapi\Http\Responder;
use Otto\Sapi\Http\Responder\Exception\ViewNotFound;
use PayloadInterop\DomainPayload;
use PayloadInterop\DomainStatus;
use Sapien\Request;
use Sapien\Response;

class ActionResponder extends Responder
{
    protected object $action;

    protected ?DomainPayload $payload = null;

    protected ?string $payloadStatus = null;

    protected ?int $responseCode = null;

    public function __invoke(
        object $action,
        ?DomainPayload $payload = null,
    ) : Response
    {
        $this->action = $action;
        $this->setPayload($payload);
        return $this->render($this->responseCode);
    }

    protected function setPayload(?DomainPayload $payload) : void
    {
        if ($payload === null) {
            return;
        }

        $this->payload = $payload;
        $this->template->addData($this->payload->getResult());
        $this->template->payload()->set($this->payload);

        $this->payloadStatus = $this->getPayloadStatus($payload);
        $this->responseCode = $this->getResponseCode($payload);
    }

    protected function getPayloadStatus(DomainPayload $payload) : string
    {
        $status = $payload->getStatus();
        $status = ucwords(str_replace('_', ' ', strtolower($status)));
        return str_replace(' ', '', $status);
    }

    protected function getResponseCode(DomainPayload $payload) : ?int
    {
        return match ($payload->getStatus()) {
            DomainStatus::ACCEPTED => 202,
            DomainStatus::CREATED => 201,
            DomainStatus::DELETED => 200,
            DomainStatus::ERROR => 500,
            DomainStatus::FOUND => 200,
            DomainStatus::INVALID => 422,
            DomainStatus::NOT_FOUND => 404,
            DomainStatus::PROCESSING => 102,
            DomainStatus::SUCCESS => 200,
            DomainStatus::UNAUTHORIZED => 403,
            DomainStatus::UPDATED => 303,
            default => null,
        };
    }

    /**
     * @inheritDoc
     */
    protected function getViews() : array
    {
        // Project\Sapi\Http\Action\Foo\Bar\Baz => Foo\Bar\GetBar
        $parts = array_slice(explode('\\', get_class($this->action)), 4);
        $name = implode(DIRECTORY_SEPARATOR, $parts);
        return [
            "action:{$name}-{$this->payloadStatus}",
            "action:{$name}",
            "status:{$this->payloadStatus}",
        ];
    }
}
