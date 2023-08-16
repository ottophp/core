<?php
declare(strict_types=1);

namespace Otto\Sapi\Http;

use AutoRoute;
use Capsule\Di\Container;
use Capsule\Di\Definitions;
use Capsule\Di\Provider;
use Sapien\Request;
use Qiq;

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
            'Sapi\\Http\\Template\\Catalog',
            'Sapi\\Http\\Template\\Helpers',
            'Sapi\\Http\\Template\\Template',
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
        $def->{Template\Catalog::class}
            ->argument('paths', [
                "{$this->directory}/resources/responder/{$this->format}/view",
                "action:{$this->directory}/resources/responder/{$this->format}/action",
                "layout:{$this->directory}/resources/responder/{$this->format}/layout",
                "layout:{$this->directory}/vendor/ottophp/core/resources/responder/{$this->format}/layout",
                "status:{$this->directory}/resources/responder/{$this->format}/status",
                "status:{$this->directory}/vendor/ottophp/core/resources/responder/{$this->format}/status",
                "throwable:{$this->directory}/resources/responder/{$this->format}/throwable",
                "throwable:{$this->directory}/vendor/ottophp/core/resources/responder/{$this->format}/throwable",
            ])
            ->argument('extension', '.qiq.php')
            ->argument('compiler', $def->get(Qiq\Compiler\QiqCompiler::class));

        $def->{Qiq\Compiler\QiqCompiler::class}
            ->argument('cachePath', $this->directory . '/tmp/cache/qiq');

        $def->{Template\Template::class}
            ->method('setLayout', $this->layout);
    }
}
