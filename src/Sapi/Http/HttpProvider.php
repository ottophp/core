<?php
declare(strict_types=1);

namespace Otto\Sapi\Http;

use AutoRoute;
use Capsule\Di\Container;
use Capsule\Di\Definitions;
use Capsule\Di\Provider;
use Otto\Sapi\Http\Responder;
use Otto\Sapi\Http\Responder\Template\ResponderTemplate;
use Otto\Sapi\Http\Responder\Template\ResponderTemplateFactory;
use Qiq;
use Sapien\Request;

class HttpProvider implements Provider
{
    protected string $directory;

    protected string $namespace;

    public function __construct(
        public readonly string $format = 'html',
        public readonly ?string $layout = 'layout:main',
        public readonly array $helpers = [],
    ) {
    }

    public function provide(Definitions $def) : void
    {
        $this->directory = $def->{'otto.directory'};
        $this->namespace = $def->{'otto.namespace'};

        $this->provideProjectClasses($def);
        $this->provideSapien($def);
        $this->provideAutoRoute($def);
        $this->provideResponderTemplate($def);
    }

    protected function provideProjectClasses(Definitions $def) : void
    {
        $suffixes = [
            'Sapi\\Http\\Front',
            'Sapi\\Http\\Responder\\FrontResponder',
            'Sapi\\Http\\Responder\\ResponderData',
        ];

        foreach ($suffixes as $suffix) {
            $defaultClass = "Otto\\{$suffix}";
            $projectClass = "{$this->namespace}\\{$suffix}";
            if (class_exists($projectClass)) {
                $def->{$defaultClass}->class($projectClass);
            }
        }
    }

    protected function provideSapien(Definitions $def) : void
    {
        $def->{Request::CLASS}
            ->argument('method', $def->call(function () {
                return $_POST['_method'] ?? null;
            }));
    }

    protected function provideAutoRoute(Definitions $def) : void
    {
        $def->{AutoRoute\Config::CLASS}
            ->arguments([
                'namespace' => $def->{'otto.namespace'} . '\\Sapi\Http\\Action',
                'directory' => $def->{'otto.directory'} . '/src/Sapi/Http/Action',
            ]);

        $def->{AutoRoute\Router::CLASS}
            ->argument('logger', $def->get(AutoRoute\Logger::CLASS));
    }

    protected function provideResponderTemplate(Definitions $def) : void
    {
        $def->{ResponderTemplateFactory::CLASS}
            ->argument('directory', $this->directory)
            ->argument('format', $this->format)
            ->argument('layout', $this->layout)
            ->argument('helpers', $this->helpers);

        $def->{ResponderTemplate::CLASS}
            ->factory($def->get(ResponderTemplateFactory::CLASS));
    }
}
