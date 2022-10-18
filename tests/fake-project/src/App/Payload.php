<?php
declare(strict_types=1);

namespace FakeProject\App;

use PayloadInterop\DomainPayload;
use PayloadInterop\DomainStatus;

class Payload implements DomainPayload, DomainStatus
{
    static public function __callStatic(string $func, array $args) : static
    {
        $status = strtoupper(
            preg_replace('/([a-z])([A-Z])/', '$1_$2', $func)
        );

        return new Payload($status, ...$args);
    }

    public function __construct(
        protected string $status,
        protected array $result = []
    ) {
    }

    public function getStatus() : string
    {
        return $this->status;
    }

    public function getResult() : array
    {
        return $this->result;
    }
}
