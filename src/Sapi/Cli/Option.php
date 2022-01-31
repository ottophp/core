<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Option
{
    public const REQUIRED = 'required';

    public const OPTIONAL = 'optional';

    public const REJECTED = 'rejected';

    public readonly array $names;

    public mixed $value = null;

    public function __construct(
        string $names,
        public readonly string $param = self::REJECTED,
        public readonly bool $multi = false,
        public readonly string $descr = '',
    ) {
        $names = explode(',', $names);

        foreach ($names as &$name) {
            $name = $this->fixName($name);
        }

        $this->names = $names;
    }

    protected function fixName(string $name) : string
    {
        $name = trim($name, '- ');

        if (strlen($name) == 1) {
            return "-$name";
        }

        return "--$name";
    }

    public function setValue(mixed $value) : void
    {
        if (! $this->multi) {
            $this->value = $value;
            return;
        }

        if ($this->value === null) {
            $this->value = [];
        }

        $this->value[] = $value;
    }

    public function getValue() : mixed
    {
        return $this->value;
    }
}
