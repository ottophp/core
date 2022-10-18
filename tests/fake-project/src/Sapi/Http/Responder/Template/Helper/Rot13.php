<?php
namespace FakeProject\Sapi\Http\Responder\Helper;

use Qiq\Escape;

class Rot13
{
    public function __construct(protected Escape $escape)
    {
    }

    public function __invoke(string $tr) : string
    {
        return $this->escape->h(str_rot13($str));
    }
}
