<?php
declare(strict_types=1);

namespace Otto\Sapi\Http\Responder\Exception;

use Otto\Sapi\Http\Responder\Exception as ResponderException;
use Otto\Sapi\Http\Template\Template;

class ViewNotFound extends ResponderException
{
    /**
     * @param string[] $views
     */
    public function __construct(
        public readonly Template $template,
        public readonly array $views,
    ) {
        $catalog = $template->getCatalog();
        $paths = $catalog->getPaths();
        $extension = $catalog->getExtension();

        $message = PHP_EOL . "Could not find any of these files ... "
            . print_r($views, true) . PHP_EOL
            . "... with this extension: {$extension}" . PHP_EOL . PHP_EOL
            . "... among these paths: "
            . print_r($paths, true) . PHP_EOL
            . "... in catalog of class " . get_class($catalog) . PHP_EOL . PHP_EOL;

        parent::__construct($message);
    }
}
