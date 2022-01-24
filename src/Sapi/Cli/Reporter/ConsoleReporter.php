<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli\Reporter;

use Otto\Sapi\Cli\Reporter;
use Throwable;
use Otto\Sapi\Cli\Result;

class ConsoleReporter extends Reporter
{
    protected Throwable $e;

    public function __invoke(Throwable $e) : Result
    {
        $this->e = $e;
        $this->template->addData(['e' => $this->template->decomposeException($e)]);
        return $this->render(500);
    }

    protected function getView() : ?string
    {
        $templateLocator = $this->template->getTemplateLocator();
        $templateLocator->setPaths($this->strategy->getPaths($this->command));

        $views = [];
        $class = get_class($this->e);

        while ($class !== false) {
            $views[] = 'console:' . str_replace('\\', DIRECTORY_SEPARATOR, $class);
            $class = get_parent_class($class);
        }

        foreach ($views as $view) {
            if ($templateLocator->has($view)) {
                return $view;
            }
        }

        return $this->strategy->viewNotFound(
            $templateLocator->getPaths(),
            $views
        );
    }
}
