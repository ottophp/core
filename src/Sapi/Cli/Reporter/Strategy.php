<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli\Reporter;

use Otto\Sapi\Cli\Result;
use Otto\Sapi\Http\Responder\Exception\ViewNotFound;

class Strategy
{
    public function __construct(
        protected string $directory,
        protected ?string $layout,
    ) {
    }

    public function getLayout() : ?string
    {
        return $this->layout;
    }

    public function viewNotFound(
        array $paths,
        array $views
    ) : ?string
    {
        throw ViewNotFound::new(
            $paths,
            $views
        );
    }

    public function getPaths() : array
    {
        return [
            "{$this->directory}/resources/reporter/view",
            "command:{$this->directory}/resources/reporter/command",
            "layout:{$this->directory}/resources/reporter/layout",
            "layout:{$this->directory}/vendor/ottophp/core/resources/reporter/layout",
            "status:{$this->directory}/resources/reporter/status",
            "status:{$this->directory}/vendor/ottophp/core/resources/reporter/status",
            "console:{$this->directory}/resources/reporter/console",
            "console:{$this->directory}/vendor/ottophp/core/resources/reporter/console",
        ];
    }

    public function newResult() : Result
    {
        return new Result();
    }
}
