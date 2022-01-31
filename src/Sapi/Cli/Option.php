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

    public function getValue() : mixed
    {
        return $this->value;
    }

    public function capture(array &$input) : void
    {
        if ($this->param === Option::REJECTED) {
            $this->setValue(true);
            return;
        }

        $value = reset($input);
        $capture = ! empty($value) && substr($value, 0, 1) !== '-';

        if ($capture) {
            $this->setValue($value);
            array_shift($input);
            return;
        }

        if ($this->param !== Option::REQUIRED) {
            $this->setValue(true);
            return;
        }

        $names = implode(',', $this->names);
        throw new Exception\OptionParamRequired(
            "The option '{$names}' requires a parameter."
        );
    }

    public function equals(string $value) : void
    {
        $value = trim($value);

        if ($this->param === self::REJECTED) {
            $this->equalsRejected($value);
            return;
        }

        if ($this->param === self::REQUIRED) {
            $this->equalsRequired($value);
        }

        $this->setValue($value === '' ? true : $value);
    }

    protected function equalsRejected(string $value) : void
    {
        if ($value === '') {
            $this->setValue(true);
            return;
        }

        $names = implode(',', $this->names);

        throw new Exception\OptionParamRejected(
            "The option '{$names}' does not accept a parameter."
        );
    }

    protected function equalsRequired(string $value) : void
    {
        if ($value !== '') {
            $this->setValue($value);
            return;
        }

        $names = implode(',', $this->names);

        throw new Exception\OptionParamRequired(
            "The option '$names' requires a parameter."
        );
    }

    protected function setValue(mixed $value) : void
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
}
