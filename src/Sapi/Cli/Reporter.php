<?php
declare(strict_types=1);

namespace Otto\Sapi\Cli;

use Otto\Sapi\Cli\Reporter\Data;
use Otto\Sapi\Cli\Reporter\Template;

abstract class Reporter
{
    public function __construct(
        protected string $directory,
        protected Template $template,
        protected Data $data
    ) {
        $this->template->getTemplateLocator()->setPaths([
            "{$this->directory}/resources/reporter/view",
            "command:{$this->directory}/resources/reporter/command",
            "layout:{$this->directory}/resources/reporter/layout",
            "layout:{$this->directory}/vendor/ottophp/core/resources/reporter/layout",
            "status:{$this->directory}/resources/reporter/status",
            "status:{$this->directory}/vendor/ottophp/core/resources/reporter/status",
            "console:{$this->directory}/resources/reporter/console",
            "console:{$this->directory}/vendor/ottophp/core/resources/reporter/console",
        ]);
        $this->template->result(new Result());
        $this->template->addData($this->data->get());
    }

    abstract protected function getView() : ?string;

    protected function render(
        int $code = null,
        string|false $view = null,
        string|false $layout = null
    ) : Result
    {
        $this->setView($view);
        $this->setLayout($layout);
        $content = ($this->template)();
        $result = $this->template->result();

        if ($result->getOutput() === null) {
            $result->setOutput($content);
        }

        if ($result->getCode() === null && $code !== null) {
            $result->setCode($code);
        }

        return $result;
    }

    protected function setView(string|false $view = null) : void
    {
        if ($view === false) {
            $this->template->setView(null);
            return;
        }

        if ($view === null) {
            $view = $this->getView();
        }

        $this->template->setView($view);
    }

    protected function setLayout(string|false $layout = null) : void
    {
        if ($layout === false) {
            $this->template->setLayout(null);
            return;
        }

        if ($layout === null) {
            // $layout = $this->strategy->getLayout();
        }

        $this->template->setLayout($layout);
    }
}
