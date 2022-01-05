<?php
namespace Otto\Sapi\Cli;

class OptionFactory
{
    public function newInstance($string) : Option
    {
        $string = trim($string);

        $option = new Option();

        $this->setNewOptionMulti($option, $string);
        $this->setNewOptionParam($option, $string);
        $this->setNewOptionMulti($option, $string);
        $this->setNewOptionNameAlias($option, $string);
        return $option;
    }

    /**
     *
     * Given an undefined option name, returns a default option struct for it.
     *
     * @param string $name The undefined option name.
     *
     * @return StdClass An option struct.
     *
     */
    public function newUndefined($name)
    {
        if (strlen($name) == 1) {
            return $this->newInstance($name);
        }

        return $this->newInstance("{$name}::");
    }

    /**
     *
     * Sets the $param property on a new option struct.
     *
     * @param StdClass $option The option struct.
     *
     * @param $string The option definition string.
     *
     * @return null
     *
     */
    protected function setNewOptionParam($option, &$string)
    {
        if (substr($string, -2) == '::') {
            $option->param = 'optional';
            $string = substr($string, 0, -2);
        } elseif (substr($string, -1) == ':') {
            $option->param = 'required';
            $string = substr($string, 0, -1);
        }

        $string = rtrim($string, ':');
    }

    /**
     *
     * Sets the $multi property on a new option struct.
     *
     * @param StdClass $option The option struct.
     *
     * @param $string The option definition string.
     *
     * @return null
     *
     */
    protected function setNewOptionMulti($option, &$string)
    {
        if (substr($string, -1) == '*') {
            $option->multi = true;
            $string = substr($string, 0, -1);
        }
    }

    /**
     *
     * Sets the $name and $alias properties on a new option struct.
     *
     * @param StdClass $option The option struct.
     *
     * @param $string The option definition string.
     *
     * @return null
     *
     */
    protected function setNewOptionNameAlias($option, &$string)
    {
        $names = explode(',', $string);
        $option->name = $this->fixOptionName($names[0]);
        if (isset($names[1])) {
            $option->alias = $this->fixOptionName($names[1]);
        }
    }

    /**
     *
     * Normalizes the option name.
     *
     * @param string $name The option character or long name.
     *
     * @return The fixed name with a leading dash or dashes.
     *
     */
    protected function fixOptionName($name)
    {
        $name = trim($name, ' -');
        if (strlen($name) == 1) {
            return "-$name";
        }
        return "--$name";
    }
}
