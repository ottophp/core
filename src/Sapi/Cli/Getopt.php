<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

use ReflectionClass;

/*

$options = Options::new($class, '__invoke');
$arguments = $this->getopt->parse($_SERVER['argv'], $options);
$command = $this->container->new($class);
return $command($options, ...$arguments);

*/
class Getopt
{
    public function parse(array $input, Options $options) : array
    {
        // flag to say when we've reached the end of options
        $done = false;

        // arguments
        $arguments = [];

        // loop through a copy of the input values to be parsed
        while ($input) {

            // shift each element from the top of the $input source
            $curr = array_shift($input);

            // after a plain double-dash, all values are arguments (not options)
            if ($curr == '--') {
                $done = true;
                continue;
            }

            if ($done) {
                $arguments[] = $curr;
                continue;
            }

            // long option?
            if (substr($curr, 0, 2) == '--') {
                $this->longOption($input, $options, ltrim($curr, '-'));
                continue;
            }

            // short option?
            if (substr($curr, 0, 1) == '-') {
                $this->shortOption($input, $options, ltrim($curr, '-'));
                continue;
            }

            // argument
            $arguments[] = $curr;
        }

        return $arguments;
    }

    protected function longOption(array &$input, Options $options, string $name) : void
    {
        $pos = strpos($name, '=');

        if ($pos !== false) {
            $value = substr($name, $pos + 1);
            $name = substr($name, 0, $pos);
            $options->getOption($name)->equals($value);
            return;
        }

        $options->getOption($name)->capture($input);
    }

    protected function shortOption(array &$input, Options $options, string $name) : void
    {
        if (strlen($name) == 1) {
            $options->getOption($name)->capture($input);
            return;
        }

        $chars = str_split($name);
        $final = array_pop($chars);

        foreach ($chars as $char) {
            $options->getOption($char)->equals('');
        }

        $options->getOption($final)->capture($input);
    }
}
