<?php
namespace Otto\Sapi\Cli;

class Option
{
    public ?string $name = null;

    public ?string $alias = null;

    public bool $multi = false;

    public ?string $param = 'rejected';

    public ?string $descr = null;

    public function __construct(string $string)
    {
        $this->setMulti($string);
        $this->setParam($string);
        $this->setMulti($string);
        $this->setNameAlias($string);
    }

    protected function setParam(&$string)
    {
        if (substr($string, -2) == '::') {
            $this->param = 'optional';
            $string = substr($string, 0, -2);
        } elseif (substr($string, -1) == ':') {
            $this->param = 'required';
            $string = substr($string, 0, -1);
        }

        $string = rtrim($string, ':');
    }

    protected function setMulti(&$string)
    {
        if (substr($string, -1) == '*') {
            $this->multi = true;
            $string = substr($string, 0, -1);
        }
    }

    protected function setNameAlias(&$string)
    {
        $names = explode(',', $string);
        $this->name = $this->fixOptionName($names[0]);
        if (isset($names[1])) {
            $this->alias = $this->fixOptionName($names[1]);
        }
    }

    protected function fixOptionName($name)
    {
        $name = trim($name, ' -');
        if (strlen($name) == 1) {
            return "-$name";
        }
        return "--$name";
    }
}
