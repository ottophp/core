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

    protected $optv = [];

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

#[Option('-f,--foo', param: Option::REQUIRED, ...)]
#[Option('-b,--bar', param: Option::OPTIONAL, ...)]
#[Option('-z,--zim')]  // NONE
*/

    public function parse(array &$input) : array
    {
        $this->input = $input;
        $this->optv = [];

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
                $this->setLongOptionValue($arg);
            } elseif (! $done && substr($arg, 0, 1) == '-') {
                $this->setShortOptionValue($arg);
            } else {
                $this->argv[$argc ++] = $arg;
            }
        }

        // done
        $input = $this->argv;
        return $this->optv;
    }

    public function getOption(string $name) : Option
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

    protected function setLongOptionValue(string $input) : void
    {
        list($name, $value) = $this->splitLongOptionInput($input);
        $option = $this->getOption($name);

        if ($this->longOptionRequiresValue($option, $value)) {
            $value = array_shift($this->input);
        }

        $this->longOptionRequiresValue($option, $value, $name)
        || $this->longOptionRejectsValue($option, $value, $name)
        || $this->setValue($option, trim((string) $value) === '' ? true : $value);
    }

    protected function splitLongOptionInput(string $input) : array
    {
        $pos = strpos($input, '=');

        if ($pos === false) {
            $name = $input;
            $value = null;
        } else {
            $name = substr($input, 0, $pos);
            $value = substr($input, $pos + 1);
        }

        return [$name, $value];
    }

    protected function longOptionRequiresValue(
        Option $option,
        mixed $value,
        ?string $name = null
    ) : bool
    {
        if ($option->param == Option::REQUIRED && trim((string) $value) === '') {
            if ($name !== null) {
                throw new Exception\OptionParamRequired(
                    "The option '$name' requires a parameter."
                );
            }

            return true;
        }

        return false;
    }

    protected function longOptionRejectsValue(
        Option $option,
        mixed $value,
        ?string $name = null
    ) : bool
    {
        if ($option->param == Option::REJECTED && trim((string) $value) !== '') {
            throw new Exception\OptionParamRejected(
                "The option '$name' does not accept a parameter."
            );
            return true;
        }
        return false;
    }

    protected function setShortOptionValue(string $name) : void
    {
        if (strlen($name) > 2) {
            $this->setShortOptionValues($name);
            return;
        }

        $option = $this->getOption($name);

        $this->shortOptionRejectsValue($option)
        || $this->shortOptionCapturesValue($option)
        || $this->shortOptionRequiresValue($option, $name)
        || $this->setValue($option, true);
    }

    protected function shortOptionRejectsValue(Option $option) : bool
    {
        if ($option->param == Option::REJECTED) {
            $this->setValue($option, true);
            return true;
        }

        return false;
    }

    protected function shortOptionCapturesValue(Option $option) : bool
    {
        $value = reset($this->input);
        $is_value = ! empty($value) && substr($value, 0, 1) != '-';

        if ($is_value) {
            $this->setValue($option, array_shift($this->input));
            return true;
        }

        return false;
    }

    protected function shortOptionRequiresValue(
        Option $option,
        string $name
    ) : bool
    {
        if ($option->param == Option::REQUIRED) {
            throw new Exception\OptionParamRequired(
                "The option '$name' requires a parameter."
            );
        }

        return false;
    }

    protected function setShortOptionValues(string $chars) : void
    {
        // drop the leading dash in the cluster and split into single chars
        $chars = str_split(substr($chars, 1));
        while ($char = array_shift($chars)) {
            $name = "-{$char}";
            $option = $this->getOption($name);
            if (! $this->shortOptionRequiresValue($option, $name)) {
                $this->setValue($option, true);
            }
        }
    }

    protected function setValue(Option $option, mixed $value) : void
    {
        if ($option->multi) {
            $this->addValue($option, $value);
            return;
        }

        foreach ($option->names as $name) {
            $name = ltrim($name, '-');
            $this->optv[$name] = $value;
        }
    }

    protected function addValue(Option $option, mixed $value) : void
    {
        foreach ($option->names as $name) {
            $name = ltrim($name, '-');
            $this->optv[$name][] = $value;
        }
    }
}
