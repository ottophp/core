<?php
declare(strict_types=1);

namespace Otto\Sapi;

use JsonSerializable;
use PayloadInterop\DomainPayload;
use Qiq\Template as QiqTemplate;
use ReflectionClass;
use Throwable;

class Template extends QiqTemplate
{
    private ?DomainPayload $payload = null;

    public function payload(DomainPayload $payload = null) : ?DomainPayload
    {
        if ($payload !== null) {
            $this->payload = $payload;
        }

        return $this->payload;
    }

    public function decomposeException(Throwable $e) : object
    {
        $vars['__CLASS__'] = get_class($e);
        $vars['__STRING__'] = (string) $e;

        $rc = new ReflectionClass($e);

        foreach ($rc->getProperties() as $rp) {
            $rp->setAccessible(true);
            $vars[$rp->getName()] = $rp->getValue($e);
        }

        $vars['trace'] = $e->getTraceAsString();
        $vars['previous'] ??= null;

        if ($vars['previous'] instanceof Throwable) {
            $vars['previous'] = $this->decomposeException($vars['previous']);
        }

        return new class($vars) implements JsonSerializable {
            public function __construct(protected readonly array $vars)
            {
            }
            public function __get(string $key) : mixed
            {
                return $this->vars[$key];
            }
            public function __toString() : string
            {
                return $this->vars['__STRING__'];
            }
            public function jsonSerialize() : mixed
            {
                return $this->vars;
            }
        };
    }
}
