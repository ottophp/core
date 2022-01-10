<?php
declare(strict_types=1);

namespace Otto\Sapi\Http;

use AutoRoute;
use Capsule\Di\Definitions;
use Capsule\Di\Provider;
use Otto\Sapi\Http\Responder;
use Otto\Sapi\Http\Responder\Strategy;
use Otto\Sapi\Http\Responder\Template;
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

        $this->provideSapien($def);
        $this->provideStrategy($def);
        $this->provideProjectClasses($def);
    }

    protected function provideProjectClasses(Definitions $def) : void
    {
        $suffixes = [
            'Sapi\\Http\\Front',
            'Sapi\\Http\\Responder\\Data',
            'Sapi\\Http\\Responder\\FrontResponder',
            'Sapi\\Http\\Responder\\Strategy\\HtmlStrategy',
            'Sapi\\Http\\Responder\\Strategy\\JsonStrategy',
            'Sapi\\Http\\Responder\\Template',
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

    protected function provideStrategy(Definitions $def) : void
    {
        $class = Strategy::CLASS . '\\' .
            ucfirst(strtolower($this->format)) . 'Strategy';

        $def->{Strategy::CLASS}
            ->arguments([
                'directory' => $this->directory,
                'layout' => $this->layout,
            ])
            ->class($class);
    }
}
