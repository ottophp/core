<?php
declare(strict_types=1);

namespace Otto;

class Exception extends \Exception
{
    protected array $info = [];

    public function getInfo() : array
    {
        return $this->info;
    }
}
