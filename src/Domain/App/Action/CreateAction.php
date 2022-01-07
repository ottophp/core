<?php
namespace Otto\Domain\App\Action;

use AutoRoute\Creator;
use Capsule\Di\Container;
use Otto\Domain\Payload;
use Psr\Log\LoggerInterface;

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
        protected LoggerInterface $logger
    ) {
    }

    public function __invoke(?string $verb, ?string $path, ?string $domain) : Payload
    {
        $this->verb = $verb;
        $this->path = $path;
        $this->domain = $domain;

        return $this->validate()
            ?? $this->createAction()
            ?? $this->createTemplate()
            ?? $this->payload('created', $this->created);
    }

    protected function validate() : ?Payload
    {
        if ($this->verb === null) {
            return $this->payload(
                'invalid',
                "Please pass an HTTP verb as the first argument."
            );
        }

        if ($this->path === null) {
            return $this->payload(
                'invalid',
                "Please pass a URL path as the second argument."
            );
        }

        if ($this->domain === null) {
            return $this->payload(
                'invalid',
                "Please pass a Domain subclass as the third argument."
            );
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

    protected function createTemplate() : ?Payload
    {
        $file = $this->created['action'];

        $mid = 'src/Sapi/Http/Action/';
        $pos = strpos($file, $mid);
        $len = strlen($mid) + $pos;

        $file = $this->directory
            . '/resources/responder/html/action/'
            . substr($file, $len);

        $code = "Template for <code>{$this->verb} {$this->path}</code>";

        return $this->write('template', $file, $code);
    }

    protected function write(string $type, string $file, string $code) : ?Payload
    {
        if (file_exists($file)) {
            return $this->payload(
                'error',
                "{$file} already exists; not overwriting.",
            );
        }

        $dir = dirname($file);

        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($file, $code);
        $this->created[$type] = $file;
        return null;
    }

    protected function payload(string $method, array|string $messages) : Payload
    {
        foreach ((array) $messages as $message) {
            $this->logger->info($message);
        }

        return Payload::$method([
            'messages' => $messages
        ]);
    }
}
