<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli\Reporter;

use Otto\Sapi\Cli\Result;
use Otto\Sapi\Http\Responder\Exception\ViewNotFound;

class Strategy
{
    protected array $packageDirs = [];

    public function __construct(
        protected string $directory,
        protected ?string $layout,
        protected array $namespaces = []
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

    public function getPaths(?object $command) : array
    {
        $paths = [];
        $packageDir = null;

        if ($command !== null) {
            $class = get_class($command);
            $namespace = strstr($class, '\\', true);
            $packageDir = $this->getPackageDir($namespace);
        }

        if ($packageDir !== null) {
            $paths[] = "{$packageDir}/resources/reporter/view";
            $paths[] = "console:{$packageDir}/resources/reporter/console";
            $paths[] = "command:{$packageDir}/resources/reporter/command";
            $paths[] = "layout:{$packageDir}/resources/reporter/layout";
            $paths[] = "status:{$packageDir}/resources/reporter/status";
        }

        $paths[] = "console:{$this->directory}/vendor/ottophp/core/resources/reporter/console";
        $paths[] = "layout:{$this->directory}/vendor/ottophp/core/resources/reporter/layout";
        $paths[] = "status:{$this->directory}/vendor/ottophp/core/resources/reporter/status";
        return $paths;
    }

    protected function getPackageDir(string $namespace) : ?string
    {
        if (empty($this->packageDirs)) {
            $this->setPackageDirs();
        }

        return $this->packageDirs[$namespace] ?? null;
    }

    protected function setPackageDirs() : void
    {
        foreach ($this->namespaces as $ns => $dir) {
            $this->setPackageDir($ns, $dir);
        }
    }

    // {$PROJECT}\Sapi\Cli : /path/to/vendor/package/src/Sapi/Cli
    // =>
    // {$PROJECT} : /path/to/vendor/package
    //
    // ...
    //
    // hypothetically, you could walk this back by the number of elements
    // in the namespace itself, so that it doesn't matter what namespace you
    // set it from as long as the namespace maps to the directory it's in.
    protected function setPackageDir(string $ns, string $dir) : void
    {
        $ns = strstr($ns, '\\', true);
        $parts = explode(DIRECTORY_SEPARATOR, $dir);
        $path = array_slice($parts, 0, -3);
        $this->packageDirs[$ns] = implode(DIRECTORY_SEPARATOR, $path);
    }

    public function newResult() : Result
    {
        return new Result();
    }
}
