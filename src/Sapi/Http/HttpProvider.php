<?php
declare(strict_types=1);

namespace Otto\Sapi\Http;

use AutoRoute;
use Capsule\Di\Container;
use Capsule\Di\Definitions;
use Capsule\Di\Provider;
use Otto\Sapi\Http\Responder\Template;
use Sapien\Request;

class HttpProvider implements Provider
{
    protected string $directory;

    protected string $namespace;

    public function __construct(
        public readonly string $format = 'html',
        public readonly ?string $layout = 'layout:main',
    ) {
    }

    public function provide(Definitions $def) : void
    {
        $this->directory = $def->{'otto.directory'};
        $this->namespace = $def->{'otto.namespace'};

        $this->provideProjectClasses($def);
        $this->provideSapien($def);
        $this->provideAutoRoute($def);
        $this->provideTemplate($def);
    }

    protected function provideProjectClasses(Definitions $def) : void
    {
        $suffixes = [
            'Sapi\\Http\\Front',
            'Sapi\\Http\\Responder\\FrontResponder',
            'Sapi\\Http\\Responder\\ResponderData',
            'Sapi\\Http\\Responder\\Template\\ResponderTemplate',
            'Sapi\\Http\\Responder\\Template\\ResponderHelpers',
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
        $def->{Request::class}
            ->argument('method', $def->call(function () {
                return $_POST['_method'] ?? null;
            }));
    }

    protected function provideAutoRoute(Definitions $def) : void
    {
        $def->{AutoRoute\Config::class}
            ->arguments([
                'namespace' => $this->namespace . '\\Sapi\Http\\Action',
                'directory' => $this->directory . '/src/Sapi/Http/Action',
            ]);

        $def->{AutoRoute\Router::class}
            ->argument('logger', $def->get(AutoRoute\Logger::class));
    }

    protected function provideTemplate(Definitions $def) : void
    {
        $def->{Template\ResponderCatalog::class}
            ->argument('directory', $this->directory)
            ->argument('format', $this->format);

        $def->{Template\ResponderCompiler::class}
            ->argument('cachePath', $this->directory . '/tmp/cache/qiq');

        $def->{Template\ResponderTemplate::class}
            ->method('setLayout', $this->layout);
    }
}
