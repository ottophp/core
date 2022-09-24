<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder;

use Otto\Sapi\Http\Responder;
use Otto\Sapi\Http\Responder\Exception\ViewNotFound;
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

    protected function getViews() : array
    {
        $views = [];
        $class = get_class($this->e);

        while ($class !== false) {
            $views[] = 'front:' . str_replace('\\', DIRECTORY_SEPARATOR, $class);
            $class = get_parent_class($class);
        }

        return $views;
    }
}
