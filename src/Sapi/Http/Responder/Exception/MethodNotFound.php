<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder\Exception;

use Otto\Sapi\Http\Responder\Exception as ResponderException;

class MethodNotFound extends ResponderException
{
    static public function new(string $class, string $method) : self
    {
        $message = "Method not found: {$class}::{$method}()";
        $e = new self($message);
        $e->info = [
            'class' => $class,
            'method' => $method,
        ];
        return $e;
    }
}
