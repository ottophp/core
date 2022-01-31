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

    protected $argv = [];

    /**
     * @param Option[] $options
     */
    public function __construct(protected array $options = [])
    {
    }

/*

How can we make an Options class the place to keep the $optv values? Then at
least you can typehint it. $options->{'a'}, $options->{'foo-bar'}, etc. Or,
$options['a'], $options['foo-bar'], etc. Want it to be readonly, which means
you have to "reach in" to the Option to set the value. Perhaps Getopt() parses
values into the Option array, then returns an Options collection?

And how to return the argument values? Maybe have an Input object, that returns
Options and has an arguments array.

$class = ...;
$optarg = Optarg::parse($argv);
// ...
$command = $container->new($class);
return $command($optarg->options, ...$optarg->arguments);

And, how to read from stdin, a la `php ./bin/console ns foo < /path/to/file` ?

ANd, instead of the :, :: syntax, maybe:

#[Option('-f,--foo', argument: Option::REQUIRED, ...)]
#[Option('-b,--bar', argument: Option::OPTIONAL, ...)]
#[Option('-z,--zim')]  // NONE
*/

    public function parse(array &$input) : array
    {
        $this->input = $input;

        // flag to say when we've reached the end of options
        $done = false;

        // sequential argument count;
        $argc = 0;

        // loop through a copy of the input values to be parsed
        while ($this->input) {

            // shift each element from the top of the $this->input source
            $arg = array_shift($this->input);

            // after a plain double-dash, all values are argv (not options)
            if ($arg == '--') {
                $done = true;
                continue;
            }

            // long option, short option, or numeric argument?
            if (! $done && substr($arg, 0, 2) == '--') {
                $this->longOption($arg);
            } elseif (! $done && substr($arg, 0, 1) == '-') {
                $this->shortOption($arg);
            } else {
                $this->argv[$argc ++] = $arg;
            }
        }

        $input = $this->argv;
        $optv = [];

        foreach ($this->options as $option) {
            foreach ($option->names as $name) {
                $name = ltrim($name, '-');
                $optv[$name] = $option->getValue();
            }
        }

        return $optv;
    }

    protected function getOption(string $name) : Option
    {
        foreach ($this->options as $option) {
            if (in_array($name, $option->names)) {
                return $option;
            }
        }

        throw new Exception\OptionNotDefined(
            "The option '$name' is not defined."
        );
    }

    protected function longOption(string $name) : void
    {
        $pos = strpos($name, '=');

        if ($pos !== false) {
            $option = $this->getOption(substr($name, 0, $pos));
            $option->equals(substr($name, $pos + 1));
            return;
        }

        $option = $this->getOption($name);
        $option->capture($this->input);
    }

    protected function shortOption(string $name) : void
    {
        if (strlen($name) == 2) {
            $option = $this->getOption($name);
            $option->capture($this->input);
            return;
        }

        $chars = str_split(substr($name, 1));
        $final = array_pop($chars);

        foreach ($chars as $char) {
            $option = $this->getOption("-{$char}");
            $option->equals('');
        }

        $option = $this->getOption("-{$final}");
        $option->capture($this->input);
    }
}
