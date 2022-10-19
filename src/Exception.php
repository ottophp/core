<?php
declare(strict_types=1);

namespace Otto;

use JsonSerializable;

class Exception extends \Exception implements JsonSerializable
{
    public function jsonSerialize() : mixed
    {
        return array_merge(
            [
                '__CLASS__' => get_class($this),
                '__STRING__' => (string) $this,
            ],
            get_object_vars($this)
        );
    }
}
