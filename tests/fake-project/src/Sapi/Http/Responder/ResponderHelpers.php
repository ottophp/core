<?php
namespace FakeProject\Sapi\Http\Responder;

use Otto\Sapi\Http\Responder\ResponderHelpers as OttoResponderHelpers;

class ResponderHelpers extends OttoResponderHelpers
{
    public function rot13(string $str) : string
    {
        return str_rot13($str);
    }
}
