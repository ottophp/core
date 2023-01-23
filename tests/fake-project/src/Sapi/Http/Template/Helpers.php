<?php
namespace FakeProject\Sapi\Http\Template;

use Otto\Sapi\Http\Template\Helpers as OttoHelpers;

class Helpers extends OttoHelpers
{
    public function rot13(string $str) : string
    {
        return str_rot13($str);
    }
}
