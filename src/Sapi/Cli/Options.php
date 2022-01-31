<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

use ReflectionClass;

class Options
{
    static public function new(string $class, string $method) : static
    {
        $options = [];
        $rc = new ReflectionClass($class);
        $rm = $rc->getMethod($method);
        $attrs = $rm->getAttributes();

        foreach ($attrs as $attr) {
            if ($attr->getName() === Option::CLASS) {
                $options[] = $attr->newInstance();
            }
        }

        return new static($options);
    }

    protected array $map = [];

    /**
     * @param Option[] $options
     */
    public function __construct(protected array $options = [])
    {
        foreach ($this->options as $option) {
            foreach ($option->names as $name) {
                $this->map[$name] = $option;
            }
        }
    }

    public function hasOption(string $name) : bool
    {
        $name = ltrim($name, '-');
        return isset($this->map[$name]);
    }

    public function getOption(string $name) : Option
    {
        $name = ltrim($name, '-');

        if ($this->hasOption($name)) {
            return $this->map[$name];
        }

        $name = strlen($name) === 1 ? "-{$name}" : "--{$name}";

        throw new Exception\OptionNotDefined(
            "$name is not defined."
        );
    }


    public function getOptions() : array
    {
        return $this->options;
    }

    public function getValue(string $name) : mixed
    {
        return $this->get($name)->getValue();
    }

    public function getValues() : array
    {
        $values = [];

        foreach ($this->map as $name => $option) {
            $values[$name] = $option->getValue();
        }

        return $values;
    }
}
