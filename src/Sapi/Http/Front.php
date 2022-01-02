<?php
namespace Otto\Sapi\Http;

use AutoRoute\Route;
use AutoRoute\Router;
use Capsule\Di\Container;
use Otto\Sapi\Http\Responder\FrontResponder;
use Otto\Sapi\Http\Responder\Template;
use Sapien\Request;
use Sapien\Response;
use Throwable;

class Front
{
    public function __construct(
        protected Container $container,
        protected Request $request,
        protected Router $router,
        protected Template $template,
        protected FrontResponder $frontResponder
    ) {
    }

    public function __invoke() : Response
    {
        try {
            $route = $this->router->route(
                (string) $this->request->method->name,
                (string) $this->request->url->path
            );

            $this->template->route($route);

            return $this->error($route) ?? $this->action($route);
        } catch (Throwable $e) {
            return ($this->frontResponder)($e);
        }
    }

    protected function error(Route $route) : ?Response
    {
        if ($route->error === null) {
            return null;
        }

        return ($this->frontResponder)($route->exception);
    }

    protected function action(Route $route) : Response
    {
        $action = $this->container->new($route->class);
        $method = $route->method;
        $arguments = $route->arguments;
        return $action->$method(...$arguments);
    }
}
