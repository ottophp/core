<?php
declare(strict_types=1);

namespace Otto;

use JsonSerializable;
use pmjones\ThrowableProperties;

class Exception extends \Exception implements JsonSerializable
{
    public function jsonSerialize() : mixed
    {
        return new ThrowableProperties($this);
    }
}
