<?php
namespace Otto\Sapi\Cli;

class Option
{
    public ?string $name = null;

    public ?string $alias = null;

    public bool $multi = false;

    public ?string $param = 'rejected';

    public ?string $descr = null;

    public function __construct(string $spec)
    {
        $this->setMulti($spec);
        $this->setParam($spec);
        $this->setMulti($spec);
        $this->setNameAlias($spec);
    }

    protected function setParam(&$spec)
    {
        if (substr($spec, -2) == '::') {
            $this->param = 'optional';
            $spec = substr($spec, 0, -2);
        } elseif (substr($spec, -1) == ':') {
            $this->param = 'required';
            $spec = substr($spec, 0, -1);
        }

        $spec = rtrim($spec, ':');
    }

    protected function setMulti(&$spec)
    {
        if (substr($spec, -1) == '*') {
            $this->multi = true;
            $spec = substr($spec, 0, -1);
        }
    }

    protected function setNameAlias(&$spec)
    {
        $names = explode(',', $spec);
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
