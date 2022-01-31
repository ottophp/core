<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

use ReflectionClass;

class Getopt
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

    protected $input = [];

    public function __construct(protected Options $options = new Options())
    {
    }

    public function parse(array &$input) : array
    {
        $this->input = $input;

        // flag to say when we've reached the end of options
        $done = false;

        // arguments
        $argv = [];

        // loop through a copy of the input values to be parsed
        while ($this->input) {

            // shift each element from the top of the $this->input source
            $arg = array_shift($this->input);

            // after a plain double-dash, all values are argv (not options)
            if ($arg == '--') {
                $done = true;
                continue;
            }

            if ($done) {
                $argv[] = $arg;
                continue;
            }

            // long option?
            if (substr($arg, 0, 2) == '--') {
                $this->longOption($arg);
                continue;
            }

            // short option?
            if (substr($arg, 0, 1) == '-') {
                $this->shortOption($arg);
                continue;
            }

            // argument
            $argv[] = $arg;
        }

        $input = $argv; // by reference!
        return $this->options->values();
    }

    protected function longOption(string $name) : void
    {
        $pos = strpos($name, '=');

        if ($pos !== false) {
            $value = substr($name, $pos + 1);
            $name = substr($name, 0, $pos);
            $this->options->get($name)->equals($value);
            return;
        }

        $this->options->get($name)->capture($this->input);
    }

    protected function shortOption(string $name) : void
    {
        if (strlen($name) == 2) {
            $this->options->get($name)->capture($this->input);
            return;
        }

        $chars = str_split(substr($name, 1));
        $final = array_pop($chars);

        foreach ($chars as $char) {
            $this->options->get("-{$char}")->equals('');
        }

        $this->options->get("-{$final}")->capture($this->input);
    }
}
