<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Option
{
    public const REQUIRED = 'required';

    public const OPTIONAL = 'optional';

    public const REJECTED = 'rejected';

    public ?array $names = [];

    public bool $multi = false;

    public ?string $param = Option::REJECTED;

    public ?string $descr = null;

    public function __construct(string $spec, ?string $descr = null)
    {
        $this->setMulti($spec); // allow before argument
        $this->setParam($spec);
        $this->setMulti($spec); // allow after argument
        $this->setNames($spec);
    }

    protected function setMulti(&$spec)
    {
        if (substr($spec, -1) == '*') {
            $this->multi = true;
            $spec = substr($spec, 0, -1);
        }
    }

    protected function setParam(&$spec)
    {
        if (substr($spec, -2) == '::') {
            $this->param = Option::OPTIONAL;
            $spec = substr($spec, 0, -2);
        } elseif (substr($spec, -1) == ':') {
            $this->param = Option::REQUIRED;
            $spec = substr($spec, 0, -1);
        }

        $spec = rtrim($spec, ':');
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
