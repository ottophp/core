<?php
namespace Otto\Sapi\Http\Template;

use Qiq\Compiler\QiqCompiler;
use Qiq\Template;

/**
 * @mixin ResponderHelpers
 */
class ResponderTemplate extends Template
{
    public function __construct(
        ResponderCatalog $catalog,
        QiqCompiler $compiler,
        ResponderHelpers $helpers
    ) {
        parent::__construct($catalog, $compiler, $helpers);
    }
}
