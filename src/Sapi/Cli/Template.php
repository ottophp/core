<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder;

use Otto\Sapi\Template as SapiTemplate;

class Template extends SapiTemplate
{
    private Result $result;

    public function result(Result $result = null) : Result
    {
        if ($result !== null) {
            $this->result = $result;
        }

        return $this->result;
    }
}
