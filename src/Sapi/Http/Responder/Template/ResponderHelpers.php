<?php
namespace Otto\Sapi\Http\Responder\Template;

use AutoRoute;
use JsonSerializable;
use Qiq\Helper\Html\HtmlHelperMethods;
use Qiq\Helper\Sapien\SapienHelperMethods;
use Qiq\Helpers;
use Throwable;

class ResponderHelpers extends Helpers
{
    use HtmlHelperMethods;
    use SapienHelperMethods;

    public function action(string $class, mixed ...$values): string
    {
        return $this
            ->get(AutoRoute\Helper::class)
            ->__invoke($class, ...$values);
    }

    public function jsonizeThrowable(Throwable $e) : JsonSerializable
    {
        return $this
            ->get(Helper\JsonizeThrowable::class)
            ->__invoke($e);
    }

    public function payload() : Helper\Payload
    {
        return $this->get(Helper\Payload::class);
    }

    public function route(): Helper\Route
    {
        return $this->get(Helper\Route::class);
    }
}
