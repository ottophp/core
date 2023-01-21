<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder\Exception;

use Otto\Sapi\Http\Responder\Exception as ResponderException;

class ViewNotFound extends ResponderException
{
    public function __construct(
        public readonly array $paths,
        public readonly array $views
    ) {
        $message = "Could not find any of these templates ... " . PHP_EOL
            . print_r($views, true)
            . "... among these paths: "
            . print_r($paths, true);

        parent::__construct($message);
    }
}
