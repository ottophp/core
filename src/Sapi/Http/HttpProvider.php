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
    public function __construct(
        public readonly string $directory,
        public readonly string $namespace,
        public readonly string $format = 'html',
        public readonly ?string $layout = 'layout:main',
        public readonly array $helpers = [],
    ) {
    }

    public function provide(Definitions $def) : void
    {
        $def->directory = $this->directory;
        $def->namespace = $this->namespace;

        $this->provideSapien($def);
        $this->provideAutoRoute($def);
        $this->provideStrategy($def);
        $this->provideTemplate($def);
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

    protected function provideAutoRoute(Definitions $def) : void
    {
        $def->{AutoRoute\Config::CLASS}
            ->arguments([
                'namespace' => $this->namespace . '\\Sapi\Http\\Action',
                'directory' => $this->directory . '/src/Sapi/Http/Action',
            ]);

        $def->{AutoRoute\Router::CLASS}
            ->argument('logger', $def->get(\AutoRoute\Logger::CLASS));
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

    protected function provideTemplate(Definitions $def) : void
    {
        $helpers = $def->array([
            'a'                     => $def->callableGet(Qiq\Helper\EscapeAttr::CLASS),
            'anchor'                => $def->callableGet(Qiq\Helper\Anchor::CLASS),
            'base'                  => $def->callableGet(Qiq\Helper\Base::CLASS),
            'button'                => $def->callableGet(Qiq\Helper\Button::CLASS),
            'c'                     => $def->callableGet(Qiq\Helper\EscapeCss::CLASS),
            'checkboxField'         => $def->callableGet(Qiq\Helper\CheckboxField::CLASS),
            'colorField'            => $def->callableGet(Qiq\Helper\ColorField::CLASS),
            'dateField'             => $def->callableGet(Qiq\Helper\DateField::CLASS),
            'datetimeField'         => $def->callableGet(Qiq\Helper\DatetimeField::CLASS),
            'datetimeLocalField'    => $def->callableGet(Qiq\Helper\DatetimeLocalField::CLASS),
            'dl'                    => $def->callableGet(Qiq\Helper\Dl::CLASS),
            'emailField'            => $def->callableGet(Qiq\Helper\EmailField::CLASS),
            'fileField'             => $def->callableGet(Qiq\Helper\FileField::CLASS),
            'form'                  => $def->callableGet(Qiq\Helper\Form::CLASS),
            'h'                     => $def->callableGet(Qiq\Helper\EscapeHtml::CLASS),
            'hiddenField'           => $def->callableGet(Qiq\Helper\HiddenField::CLASS),
            'image'                 => $def->callableGet(Qiq\Helper\Image::CLASS),
            'imageButton'           => $def->callableGet(Qiq\Helper\ImageButton::CLASS),
            'inputField'            => $def->callableGet(Qiq\Helper\InputField::CLASS),
            'items'                 => $def->callableGet(Qiq\Helper\Items::CLASS),
            'j'                     => $def->callableGet(Qiq\Helper\EscapeJs::CLASS),
            'label'                 => $def->callableGet(Qiq\Helper\Label::CLASS),
            'link'                  => $def->callableGet(Qiq\Helper\Link::CLASS),
            'linkStylesheet'        => $def->callableGet(Qiq\Helper\LinkStylesheet::CLASS),
            'meta'                  => $def->callableGet(Qiq\Helper\Meta::CLASS),
            'metaHttp'              => $def->callableGet(Qiq\Helper\MetaHttp::CLASS),
            'metaName'              => $def->callableGet(Qiq\Helper\MetaName::CLASS),
            'monthField'            => $def->callableGet(Qiq\Helper\MonthField::CLASS),
            'numberField'           => $def->callableGet(Qiq\Helper\NumberField::CLASS),
            'ol'                    => $def->callableGet(Qiq\Helper\Ol::CLASS),
            'passwordField'         => $def->callableGet(Qiq\Helper\PasswordField::CLASS),
            'radioField'            => $def->callableGet(Qiq\Helper\RadioField::CLASS),
            'rangeField'            => $def->callableGet(Qiq\Helper\RangeField::CLASS),
            'resetButton'           => $def->callableGet(Qiq\Helper\ResetButton::CLASS),
            'script'                => $def->callableGet(Qiq\Helper\Script::CLASS),
            'searchField'           => $def->callableGet(Qiq\Helper\SearchField::CLASS),
            'select'                => $def->callableGet(Qiq\Helper\Select::CLASS),
            'submitButton'          => $def->callableGet(Qiq\Helper\SubmitButton::CLASS),
            'telField'              => $def->callableGet(Qiq\Helper\TelField::CLASS),
            'textarea'              => $def->callableGet(Qiq\Helper\Textarea::CLASS),
            'textField'             => $def->callableGet(Qiq\Helper\TextField::CLASS),
            'timeField'             => $def->callableGet(Qiq\Helper\TimeField::CLASS),
            'u'                     => $def->callableGet(Qiq\Helper\EscapeUrl::CLASS),
            'ul'                    => $def->callableGet(Qiq\Helper\Ul::CLASS),
            'urlField'              => $def->callableGet(Qiq\Helper\UrlField::CLASS),
            'weekField'             => $def->callableGet(Qiq\Helper\WeekField::CLASS),
            'action'                => $def->callableGet(AutoRoute\Helper::CLASS),
        ]);

        foreach ($this->helpers as $name => $class) {
            $helpers[$name] = $def->callableGet($class);
        }

        $def->{Template::CLASS . ':compiler'} = $def
            ->newDefinition(Qiq\Compiler\QiqCompiler::CLASS)
            ->inherit(null)
            ->argument('cachePath', "{$this->directory}/tmp/cache/qiq");

        $def->{Template::CLASS. ':templateLocator'} = $def
            ->newDefinition(Qiq\TemplateLocator::CLASS)
            ->inherit(null)
            ->arguments([
                'paths' => [],
                'extension' => '.php',
                'compiler' => $def->get(Template::CLASS . ':compiler'),
            ]);

        $def->{Template::CLASS . ':helperLocator'} = $def
            ->newDefinition(Qiq\HelperLocator::CLASS)
            ->inherit(null)
            ->argument('factories', $helpers);

        $def->{Template::CLASS}
            ->inherit(null)
            ->arguments([
                $def->get(Template::CLASS . ':templateLocator'),
                $def->get(Template::CLASS . ':helperLocator'),
                $def->get(Request::CLASS),
            ]);
    }
}
