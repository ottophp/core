<?php
namespace Otto\Sapi\Http\Responder\Template;

use Qiq\Catalog;

class ResponderCatalog extends Catalog
{
	public function __construct(string $directory, string $format)
	{
		parent::__construct(
			paths: [
                "{$directory}/resources/responder/{$format}/view", // rename to /default ?
                "action:{$directory}/resources/responder/{$format}/action",
                "layout:{$directory}/resources/responder/{$format}/layout",
                "layout:{$directory}/vendor/ottophp/core/resources/responder/{$format}/layout",
                "status:{$directory}/resources/responder/{$format}/status",
                "status:{$directory}/vendor/ottophp/core/resources/responder/{$format}/status",
                "front:{$directory}/resources/responder/{$format}/front",
                "front:{$directory}/vendor/ottophp/core/resources/responder/{$format}/front",
            ],
            extension: '.qiq.php'
        );
	}

    public function has(string $name) : bool
    {
        return null !== $this->find($name);
    }
}
