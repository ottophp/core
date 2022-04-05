<?php
declare(strict_types=1);

namespace Otto\Sapi\Http;

use Otto\Sapi\Http\Responder\Data;
use Otto\Sapi\Http\Responder\Exception;
use Otto\Sapi\Http\Responder\Strategy;
use Otto\Sapi\Http\Responder\Template;
use Sapien\Request;
use Sapien\Response;
use PayloadInterop\DomainPayload;
use Throwable;

class Responder
{
    /**
     * put a view stack in qiq, instead of here?
     * e.g. if $this->setView(['foo', 'bar', 'baz']) look for the first
     * existing one and use it. that gets the logic out of here and into
     * the template system itself. will need related error reporting at
     * the qiq level.
     */
    protected array $views = [];

    public function __construct(
        protected Request $request,
        protected Template $template,
        protected Strategy $strategy,
        protected Data $data // data is a terrible name
    ) {
        $this->template->getTemplateLocator()->setPaths($this->strategy->getPaths());
        $this->template->response($this->strategy->newResponse());
        $this->template->addData($this->data->get());
    }

    public function __invoke(
        /* Front|Action */ object $origin,
        DomainPayload|Throwable $spec = null
    ) : Response
    {
        $this->views = [];

        if ($spec instanceof Throwable) {
            return $this->__invokeThrowable($spec);
        }

        return $this->__invokePayload($origin, $spec);
    }

    protected function __invokePayload(
        object $action,
        ?DomainPayload $payload
    ) : Response
    {
        $status = null;

        if ($payload !== null) {
            $this->template->addData($payload->getResult());
            $this->template->payload($payload);
            $status = $payload->getStatus();
            $status = ucwords(str_replace('_', ' ', strtolower($status)));
            $status = str_replace(' ', '', $status);
        }

        $parts = array_slice(explode('\\', get_class($action)), 4);
        $name = implode(DIRECTORY_SEPARATOR, $parts);
        $this->views = [
            "action:{$name}-{$status}",
            "action:{$name}",
            "status:{$status}",
        ];
        $method = "respond{$status}";

        if (! method_exists($this, $method)) {
            throw Exception\MethodNotFound::new(get_class($this), $method);
        }

        return $this->$method();
    }

    protected function __invokeThrowable(Throwable $e) : Response
    {
        $this->views = [];

        $this->template->addData([
            'e' => $this->template->decomposeException($e)
        ]);

        $class = get_class($e);

        while ($class !== false) {
            $name = str_replace('\\', DIRECTORY_SEPARATOR, $class);
            $this->views[] = "throwable:{$name}";
            $class = get_parent_class($class);
        }

        return $this->respondError();
    }

    protected function setView(string|false $view = null) : void
    {
        if ($view === false) {
            $this->template->setView(null);
            return;
        }

        if ($view !== null) {
            $this->template->setView($view);
            return;
        }

        $templateLocator = $this->template->getTemplateLocator();

        foreach ($this->views as $view) {
            if ($templateLocator->has($view)) {
                $this->template->setView($view);
                return;
            }
        }

        $this->template->setView($this->strategy->viewNotFound(
            $templateLocator->getPaths(),
            $this->views
        ));
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

    protected function respond(
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

    protected function respondAccepted() : Response
    {
        return $this->respond(202);
    }

    protected function respondCreated() : Response
    {
        return $this->respond(201);
    }

    protected function respondDeleted() : Response
    {
        return $this->respond(200);
    }

    protected function respondError() : Response
    {
        return $this->respond(500);
    }

    protected function respondFound() : Response
    {
        return $this->respond(200);
    }

    protected function respondInvalid() : Response
    {
        return $this->respond(422);
    }

    protected function respondNotFound() : Response
    {
        return $this->respond(404);
    }

    protected function respondProcessing() : Response
    {
        return $this->respond(102);
    }

    protected function respondSuccess() : Response
    {
        return $this->respond(200);
    }

    protected function respondUnauthorized() : Response
    {
        return $this->respond(400);
    }

    protected function respondUpdated() : Response
    {
        return $this->respond(303);
    }
}
