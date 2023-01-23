{{ /** @var \Otto\Sapi\Http\Template\Template $this */ }}
{{ addData(['route' => $this->route()->get()]) }}
{{ response()->setCode(404) }}
