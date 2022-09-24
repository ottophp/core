<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder\Template\Helper;

use PayloadInterop\DomainPayload;

class Payload
{
    protected ?DomainPayload $payload = null;

    public function __call(string $func, array $args) : mixed
    {
        return $this->payload !== null
            ? $this->payload->$func(...$args)
            : null;
    }

    public function __invoke() : static
    {
        return $this;
    }

    public function set(?DomainPayload $payload) : void
    {
        $this->payload = $payload;
    }

    public function get() : ?DomainPayload
    {
        return $this->payload;
    }
}
