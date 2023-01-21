<?php
declare(strict_types=1);

namespace Otto\Sapi\Http;

use Otto\Sapi\Http\Responder\Exception\ViewNotFound;
use Otto\Sapi\Http\Responder\ResponderData;
use Otto\Sapi\Http\Responder\Template\ResponderTemplate;
use Qiq\Template;
use Sapien\Request;
use Sapien\Response;

abstract class Responder
{
    public function __construct(
        protected Request $request,
        protected ResponderTemplate $template,
        protected ResponderData $responderData
    ) {
        $this->template->addData($responderData->get());
    }

    protected function render(
        int $code = null,
        string|false $view = null,
        string|false $layout = null
    ) : Response
    {
        $this->setView($view);
        $this->setLayout($layout);
        $response = $this->template->response()->render($this->template);

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

    protected function getView() : ?string
    {
        $catalog = $this->template->getCatalog();

        $views = $this->getViews();

        foreach ($views as $view) {
            if ($catalog->has($view)) {
                return $view;
            }
        }

        throw new ViewNotFound(
            $catalog->getPaths(),
            $views
        );
    }

    protected function setLayout(string|false $layout = null) : void
    {
        if ($layout === false) {
            $this->template->setLayout(null);
            return;
        }

        if ($layout !== null) {
            $this->template->setLayout($layout);
        }
    }

    abstract protected function getViews() : array;
}
