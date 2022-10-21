<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder\Template\Helper;

use JsonSerializable;
use pmjones\ThrowableProperties;
use Throwable;

class JsonizeThrowable
{
    public function __invoke(Throwable $e) : JsonSerializable
    {
        if ($e instanceof JsonSerializable || $e instanceof ThrowableProperties) {
            return $e;
        }

        return new ThrowableProperties($e);
    }
}
