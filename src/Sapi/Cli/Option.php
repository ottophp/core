<?php
namespace Otto\Sapi\Cli;

class Option
{
    public ?array $names = [];

    public ?string $alias = null;

    public bool $multi = false;

    public ?string $param = 'rejected';

    public ?string $descr = null;

    public function __construct(string $spec)
    {
        $this->setMulti($spec);
        $this->setParam($spec);
        $this->setMulti($spec);
        $this->setNames($spec);
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

    protected function setNames(&$spec)
    {
        $this->names = explode(',', $spec);
        foreach ($this->names as &$name) {
            $name = $this->fixName($name);
        }
    }

    protected function fixName($name)
    {
        $name = trim($name, ' -');
        if (strlen($name) == 1) {
            return "-$name";
        }
        return "--$name";
    }
}
