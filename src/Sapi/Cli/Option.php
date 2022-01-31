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
        protected bool $multiple = false,
        protected ?string $type = null,
    ) {
        $names = explode('|', $names);

        foreach ($names as &$name) {
            $name = trim($name, '- ');
        }

        $this->names = $names;
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

        throw new Exception\OptionParamRequired(
            "{$this->dashedNames()} requires an argument."
        );
    }

    protected function dashedNames() : string
    {
        $dashed = [];
        foreach ($this->names as $name) {
            $dashed[] = strlen($name) === 1 ? "-{$name}" : "--{$name}";
        }
        return implode('|', $dashed);
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

        throw new Exception\OptionParamRejected(
            "{$this->dashedNames()} does not accept an argument."
        );
    }

    protected function equalsRequired(string $value) : void
    {
        if ($value !== '') {
            $this->setValue($value);
            return;
        }

        throw new Exception\OptionParamRequired(
            "{$this->dashedNames()} requires an argument."
        );
    }

    protected function setValue(mixed $value) : void
    {
        $value = $this->cast($value);

        if (! $this->multiple) {
            $this->value = $value;
            return;
        }

        if ($this->value === null) {
            $this->value = [];
        }

        $this->value[] = $value;
    }

    protected function cast(mixed $value) : mixed
    {
        if ($this->type === null) {
            return $value;
        }

        if (class_exists($this->type)) {
            return $this->toObject($value);
        }

        $method = 'to' . ucfirst($this->type);
        return $this->$method($value);
    }

    protected function toArray(mixed $value) : array
    {
        if (is_array($value)) {
            return $value;
        }

        return str_getcsv((string) $value);
    }

    protected function toBool(mixed $value) : bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (in_array(strtolower($value), ['1', 't', 'true', 'y', 'yes'])) {
            return true;
        }

        if (in_array(strtolower($value), ['0', 'f', 'false', 'n', 'no'])) {
            return false;
        }

        throw $this->invalidArgument($rp, 'boolean-equivalent', $value);
    }

    protected function toInt(mixed $value) : int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value) && (int) $value == $value) {
            return (int) $value;
        }

        throw $this->invalidArgument($rp, 'numeric integer', $value);
    }

    protected function toFloat(mixed $value) : float
    {
        if (is_float($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        throw $this->invalidArgument($rp, 'numeric float', $value);
    }

    protected function toObject(mixed $value) : object
    {
        $class = $this->type;
        return new $class($value);
    }
}
