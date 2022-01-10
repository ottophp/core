<?php
declare(strict_types=1);

namespace Otto\Sapi;

use AutoRoute;
use Capsule\Di\Definitions;
use Capsule\Di\Provider;
use Qiq;

class SapiProvider implements Provider
{
    public function __construct(
        public readonly array $helpers = [],
    ) {
    }

    public function provide(Definitions $def) : void
    {
        $this->provideAutoRoute($def);
        $this->provideTemplate($def);
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

        $def->{Qiq\Compiler\QiqCompiler::CLASS}
            ->argument('cachePath', $def->{'otto.directory'} . "/tmp/cache/qiq");

        $def->{Qiq\TemplateLocator::CLASS}
            ->arguments([
                'paths' => [],
                'extension' => '.php',
                'compiler' => $def->get(Qiq\Compiler\QiqCompiler::CLASS),
            ]);

        $def->{Qiq\HelperLocator::CLASS}
            ->argument('factories', $helpers);
    }
}
