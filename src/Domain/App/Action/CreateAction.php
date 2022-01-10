<?php
namespace Otto\Domain\App\Action;

use AutoRoute\Creator;
use Capsule\Di\Container;
use Otto\Domain\Payload;

class CreateAction
{
    protected ?string $verb;

    protected ?string $path;

    protected ?string $domain;

    protected array $created = [];

    public function __construct(
        protected Creator $creator,
        protected string $directory,
        protected string $namespace,
    ) {
    }

    public function __invoke(?string $verb, ?string $path, ?string $domain) : Payload
    {
        $this->verb = $verb;
        $this->path = $path;
        $this->domain = $domain;

        return $this->validate()
            ?? $this->createAction()
            ?? $this->createHtmlTemplate()
            ?? $this->createJsonTemplate()
            ?? $this->created();
    }

    protected function validate() : ?Payload
    {
        if ($this->verb === null) {
            return Payload::invalid([
                'messages' => [
                    "Please pass an HTTP verb as the first argument.",
                ],
            ]);
        }

        if ($this->path === null) {
            return Payload::invalid([
                'messages' => [
                    "Please pass a URL path as the second argument.",
                ],
            ]);
        }

        if ($this->domain === null) {
            return Payload::invalid([
                'messages' => [
                    "Please pass a Domain subclass as the third argument.",
                ],
            ]);
        }

        return null;
    }

    protected function createAction() : ?Payload
    {
        $template = $this->directory . '/resources/action.tpl';

        [$file, $code] = $this->creator->create(
            $this->verb,
            $this->path,
            file_get_contents($template)
        );

        $code = strtr($code, ['{DOMAIN}' => $this->domain]);

        return $this->write('action', $file, $code);
    }

    protected function createHtmlTemplate() : ?Payload
    {
        $file = $this->created['action'];

        $mid = 'src/Sapi/Http/Action/';
        $pos = strpos($file, $mid);
        $len = strlen($mid) + $pos;

        $file = $this->directory
            . '/resources/responder/html/action/'
            . substr($file, $len);

        $code = "HTML template for <code>{$this->verb} {$this->path}</code>" . PHP_EOL;

        return $this->write('template', $file, $code);
    }

    protected function createJsonTemplate() : ?Payload
    {
        $file = $this->created['action'];

        $mid = 'src/Sapi/Http/Action/';
        $pos = strpos($file, $mid);
        $len = strlen($mid) + $pos;

        $file = $this->directory
            . '/resources/responder/json/action/'
            . substr($file, $len);

        $code = "<?php /* JSON template for {$this->verb} {$this->path} */" . PHP_EOL;

        return $this->write('template', $file, $code);
    }

    protected function write(string $type, string $file, string $code) : ?Payload
    {
        if (file_exists($file)) {
            return Payload::error([
                'messages' => [
                    "{$file} already exists; not overwriting.",
                ],
            ]);
        }

        $dir = dirname($file);

        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($file, $code);
        $this->created[$type] = $file;
        return null;
    }

    protected function created() : Payload
    {
        return Payload::created([
            'messages' => $this->created
        ]);
    }
}
