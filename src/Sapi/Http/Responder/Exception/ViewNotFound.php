<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder\Exception;

use Otto\Sapi\Http\Responder\Exception as ResponderException;

class ViewNotFound extends ResponderException
{
    public readonly array $paths;

    public readonly array $views;

    static public function new(
        array $paths,
        array $views
    ) : self
    {
        $message = "Could not find any of these templates ... " . PHP_EOL
            . print_r($views, true)
            . "... among these paths: "
            . print_r($paths, true);
        $e = new self($message);
        $e->paths = $paths;
        $e->views = $views;
        return $e;
    }
}
