<?php
namespace FakeProject\Sapi\Http\Template;

use Otto\Sapi\Http\Template\ResponderHelpers as OttoResponderHelpers;

class ResponderHelpers extends OttoResponderHelpers
{
    public function rot13(string $str) : string
    {
        return str_rot13($str);
    }
}
