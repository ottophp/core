<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder\Template\Helper;

use JsonSerializable;
use ReflectionClass;
use Throwable;

class DecomposeException
{
    public function __invoke(?Throwable $e) : object
    {
        $vars = [];

        if ($e) {
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
                $vars['previous'] = $this($vars['previous']);
            }
        }

        return new class($vars) implements JsonSerializable
        {
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
