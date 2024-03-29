<?php
namespace Otto\Sapi\Http\Template;

use AutoRoute;
use Capsule\Di\Container;
use JsonSerializable;
use Qiq\Helper\Html\HtmlHelperMethods;
use Qiq\Helper\Sapien\SapienHelperMethods;
use Qiq\Helpers as QiqHelpers;
use Qiq\Helper\Sapien as SapienHelper;
use Sapien;
use Throwable;

class Helpers extends QiqHelpers
{
    use HtmlHelperMethods;
    use SapienHelperMethods;

    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

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
