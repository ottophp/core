<?php
declare(strict_types=1);

namespace Otto\Sapi\Http;

use Otto\Sapi\Cli\Reporter\Data;
use Otto\Sapi\Cli\Reporter\Template;

abstract class Reporter
{
    public function __construct(
        string $directory,
        protected Template $template,
        protected Data $data
    ) {
        $this->template->getTemplateLocator()->setPaths(
            "{$this->directory}/resources/responder/html/view",
            "command:{$this->directory}/resources/responder/html/action",
            "layout:{$this->directory}/resources/responder/html/layout",
            "layout:{$this->directory}/vendor/ottophp/core/resources/responder/html/layout",
            "status:{$this->directory}/resources/responder/html/status",
            "status:{$this->directory}/vendor/ottophp/core/resources/responder/html/status",
            "console:{$this->directory}/resources/responder/html/front",
            "console:{$this->directory}/vendor/ottophp/core/resources/responder/html/front",
        );
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
            $layout = $this->strategy->getLayout();
        }

        $this->template->setLayout($layout);
    }
}
