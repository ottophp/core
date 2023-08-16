<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Template\Helper;

use AutoRoute\Route as CurrentRoute;

/**
 * @mixin CurrentRoute
 */
class Route
{
    protected ?CurrentRoute $route = null;

    public function __get(string $prop) : mixed
    {
        return $this->route !== null
            ? $this->route->$prop
            : null;
    }

    /**
     * @param mixed[] $args
     */
    public function __call(string $func, array $args) : mixed
    {
        return $this->route !== null
            ? $this->route->$func(...$args)
            : null;
    }

    public function __invoke() : static
    {
        return $this;
    }

    public function set(?CurrentRoute $route) : void
    {
        $this->route = $route;
    }

    public function get() : ?CurrentRoute
    {
        return $this->route;
    }
}
