<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

class Options
{
    static public function new(string $class, string $method) : static
    {
        $list = [];
        $rc = new ReflectionClass($class);
        $rm = $rc->getMethod($method);
        $attrs = $rm->getAttributes();

        foreach ($attrs as $attr) {
            if ($attr->getName() === Option::CLASS) {
                $list[] = $attr->newInstance();
            }
        }

        return new static($list);
    }

    protected array $map = [];

    /**
     * @param Option[] $list
     */
    public function __construct(protected array $list = [])
    {
        foreach ($this->list as $option) {
            foreach ($option->names as $name) {
                $this->map[$name] = $option;
            }
        }
    }

    public function has(string $name) : bool
    {
        return isset($this->map[$name]);
    }

    public function get(string $name) : Option
    {
        if ($this->has($name)) {
            return $this->map[$name];
        }

        $name = strlen($name) === 1 ? "-{$name}" : "--{$name}";

        throw new Exception\OptionNotDefined(
            "The option '$name' is not defined."
        );
    }

    public function values() : array
    {
        $values = [];

        foreach ($this->map as $name => $option) {
            $name = ltrim($name, '-');
            $values[$name] = $option->getValue();
        }

        return $values;
    }

    public function value(string $name) : mixed
    {
        return $this->get($name)->getValue();
    }
}
