<?php
namespace Otto\Sapi\Http\Template;

use Qiq\Template as QiqTemplate;

/**
 * @mixin Helpers
 */
class Template extends QiqTemplate
{
    public function __construct(
        Catalog $catalog,
        Helpers $helpers
    ) {
        parent::__construct($catalog, $helpers);
    }
}
