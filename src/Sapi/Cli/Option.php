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

    protected mixed $value = null;

    public function __construct(
        string $names,
        protected string $argument = self::REJECTED,
        protected bool $multiple = false
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
        if ($this->argument === Option::REJECTED) {
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

        if ($this->argument !== Option::REQUIRED) {
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

        if ($this->argument === self::REJECTED) {
            $this->equalsRejected($value);
            return;
        }

        if ($this->argument === self::REQUIRED) {
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
        if (! $this->multiple) {
            $this->value = $value;
            return;
        }

        if ($this->value === null) {
            $this->value = [];
        }

        $this->value[] = $value;
    }
}
