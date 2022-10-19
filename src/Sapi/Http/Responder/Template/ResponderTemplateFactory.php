<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder\Template;

use AutoRoute;
use Capsule\Di\Container;
use Qiq;
use Qiq\Escape;
use Qiq\HelperLocator;

class ResponderTemplateFactory
{
    public function __construct(
        protected string $directory,
        protected string $format = 'html',
        protected ?string $layout = 'layout:main',
        protected array $helpers = [],
    ) {
    }

    public function __invoke(Container $container) : ResponderTemplate
    {
        $paths = [
            "{$this->directory}/resources/responder/{$this->format}/view",
            "action:{$this->directory}/resources/responder/{$this->format}/action",
            "layout:{$this->directory}/resources/responder/{$this->format}/layout",
            "layout:{$this->directory}/vendor/ottophp/core/resources/responder/{$this->format}/layout",
            "status:{$this->directory}/resources/responder/{$this->format}/status",
            "status:{$this->directory}/vendor/ottophp/core/resources/responder/{$this->format}/status",
            "front:{$this->directory}/resources/responder/{$this->format}/front",
            "front:{$this->directory}/vendor/ottophp/core/resources/responder/{$this->format}/front",
        ];

        $helperLocator = HelperLocator::new($container->new(Escape::CLASS));

        $helpers = array_merge(
            [
                'action' => AutoRoute\Helper::CLASS,
                'request' => Qiq\Helper\Sapien\Request::CLASS,
                'response' => Qiq\Helper\Sapien\Response::CLASS,
                'route' => Helper\Route::CLASS,
                'payload' => Helper\Payload::CLASS,
                'throwableProperties' => Helper\ThrowableProperties::CLASS,
            ],
            $this->helpers
        );

        foreach ($helpers as $name => $class) {
            $helperLocator->set($name, $container->callableNew($class));
        }

        $template = ResponderTemplate::new(
            paths: $paths,
            extension: '.php',
            encoding: 'utf-8',
            cachePath: "{$this->directory}/tmp/cache/qiq",
            helperLocator: $helperLocator,
        );

        $template->setLayout($this->layout);

        return $template;
    }
}
