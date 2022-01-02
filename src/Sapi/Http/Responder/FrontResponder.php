<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder;

use Otto\Sapi\Http\Responder;
use Sapien\Response;
use Throwable;

class FrontResponder extends Responder
{
    protected Throwable $e;

    public function __invoke(Throwable $e) : Response
    {
        $this->e = $e;
        $this->template->addData(['e' => $this->template->decomposeException($e)]);
        return $this->render(500);
    }

    protected function getView() : ?string
    {
        $templateLocator = $this->template->getTemplateLocator();

        $views = [];
        $class = get_class($this->e);

        while ($class !== false) {
            $views[] = 'front:' . str_replace('\\', DIRECTORY_SEPARATOR, $class);
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
