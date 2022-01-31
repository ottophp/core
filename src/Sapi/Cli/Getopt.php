<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

use ReflectionClass;

class Getopt
{
    public function parse(array &$input, Options $options) : array
    {
        // flag to say when we've reached the end of options
        $done = false;

        // arguments
        $argv = [];

        // loop through a copy of the input values to be parsed
        while ($input) {

            // shift each element from the top of the $input source
            $arg = array_shift($input);

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
                $this->longOption($input, $options, ltrim($arg, '-'));
                continue;
            }

            // short option?
            if (substr($arg, 0, 1) == '-') {
                $this->shortOption($input, $options, ltrim($arg, '-'));
                continue;
            }

            // argument
            $argv[] = $arg;
        }

        $input = $argv; // by reference!
        return $options->values();
    }

    protected function longOption(array &$input, Options $options, string $name) : void
    {
        $pos = strpos($name, '=');

        if ($pos !== false) {
            $value = substr($name, $pos + 1);
            $name = substr($name, 0, $pos);
            $options->get($name)->equals($value);
            return;
        }

        $options->get($name)->capture($input);
    }

    protected function shortOption(array &$input, Options $options, string $name) : void
    {
        if (strlen($name) == 1) {
            $options->get($name)->capture($input);
            return;
        }

        $chars = str_split($name);
        $final = array_pop($chars);

        foreach ($chars as $char) {
            $options->get($char)->equals('');
        }

        $options->get($final)->capture($input);
    }
}
