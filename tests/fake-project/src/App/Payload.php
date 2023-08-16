<?php
declare(strict_types=1);

namespace FakeProject\App;

use PayloadInterop\DomainPayload;
use PayloadInterop\DomainStatus;

class Payload implements DomainPayload, DomainStatus
{
    /**
     * @param mixed[] $result
     */
    public static function accepted(array $result = []) : self
    {
        return new self(DomainStatus::ACCEPTED, $result);
    }

    /**
     * @param mixed[] $result
     */
    public static function created(array $result = []) : self
    {
        return new self(DomainStatus::CREATED, $result);
    }

    /**
     * @param mixed[] $result
     */
    public static function deleted(array $result = []) : self
    {
        return new self(DomainStatus::DELETED, $result);
    }

    /**
     * @param mixed[] $result
     */
    public static function error(array $result = []) : self
    {
        return new self(DomainStatus::ERROR, $result);
    }

    /**
     * @param mixed[] $result
     */
    public static function found(array $result = []) : self
    {
        return new self(DomainStatus::FOUND, $result);
    }

    /**
     * @param mixed[] $result
     */
    public static function invalid(array $result = []) : self
    {
        return new self(DomainStatus::INVALID, $result);
    }

    /**
     * @param mixed[] $result
     */
    public static function notFound(array $result = []) : self
    {
        return new self(DomainStatus::NOT_FOUND, $result);
    }

    /**
     * @param mixed[] $result
     */
    public static function processing(array $result = []) : self
    {
        return new self(DomainStatus::PROCESSING, $result);
    }

    /**
     * @param mixed[] $result
     */
    public static function success(array $result = []) : self
    {
        return new self(DomainStatus::SUCCESS, $result);
    }

    /**
     * @param mixed[] $result
     */
    public static function unauthorized(array $result = []) : self
    {
        return new self(DomainStatus::UNAUTHORIZED, $result);
    }

    /**
     * @param mixed[] $result
     */
    public static function updated(array $result = []) : self
    {
        return new self(DomainStatus::UPDATED, $result);
    }

    /**
     * @param mixed[] $result
     */
    public function __construct(
        protected string $status,
        protected array $result = []
    ) {
    }

    public function getStatus() : string
    {
        return $this->status;
    }

    /**
     * @return mixed[]
     */
    public function getResult() : array
    {
        return $this->result;
    }
}
