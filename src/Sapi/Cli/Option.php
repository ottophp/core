<?php
namespace Otto\Sapi\Cli;

class Option
{
    public function __construct(
        public ?string $name = null,
        public ?string $alias = null,
        public bool $multi = false,
        public ?string $param = 'rejected',
        public ?string $descr = null,
    ) {
    }
}
