<?php
declare(strict_types=1);

namespace Otto\Sapi\Http;

use Otto\Sapi\Http\Responder\Data;
use Otto\Sapi\Http\Responder\Strategy;
use Otto\Sapi\Http\Responder\Template;
use Sapien\Request;
use Sapien\Response;

abstract class Responder
{
    public function __construct(
        protected Request $request,
        protected Template $template,
        protected Strategy $strategy,
        protected Data $data
    ) {
        $this->template->getTemplateLocator()->setPaths($this->strategy->getPaths());
        $this->template->response($this->strategy->newResponse());
        $this->template->addData($this->data->get());
    }

    abstract protected function getView() : ?string;

    protected function render(
        int $code = null,
        string|false $view = null,
        string|false $layout = null
    ) : Response
    {
        $this->setView($view);
        $this->setLayout($layout);
        $content = ($this->template)();
        $response = $this->template->response();

        if ($response->getContent() === null) {
            $response->setContent($content);
        }

        if ($this->request->method->is('HEAD')) {
            $response->setContent(null);
        }

        if ($response->getCode() === null && $code !== null) {
            $response->setCode($code);
        }

        return $response;
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
