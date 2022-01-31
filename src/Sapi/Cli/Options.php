<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

class Options
{
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

    public function has(string $name) : bool
    {
        return isset($this->map[$name]);
    }

    public function get(string $name) : Option
    {
        if ($this->has($name)) {
            return $this->map[$name];
        }

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
}
