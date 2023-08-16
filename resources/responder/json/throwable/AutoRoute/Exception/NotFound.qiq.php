{{ /** @var \Otto\Sapi\Http\Template\Template $this */ }}
{{ addData(['route' => route()->get()]) }}
{{ response()->setCode(404) }}
