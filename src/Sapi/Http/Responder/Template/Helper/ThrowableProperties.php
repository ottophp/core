<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder\Template\Helper;

use JsonSerializable;
use ReflectionClass;
use stdClass;
use Throwable;

class ThrowableProperties extends stdClass
{
    public function __invoke(Throwable $e) : static
    {
        $self = new static();
        $self->__CLASS__ = get_class($e);
        $self->__STRING__ = (string) $e;
        $self->__TRACE__ = $e->getTraceAsString();

        $rc = new ReflectionClass($e);

        foreach ($rc->getProperties() as $rp) {
            $rp->setAccessible(true);
            $name = $rp->getName();
            $self->$name = $rp->getValue($e);
        }

        $self->__PREVIOUS__ = $e->getPrevious() === null
            ? null
            : $this($e->getPrevious());

        return $self;
    }
}
