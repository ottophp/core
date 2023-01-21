{{ /** @var \Qiq\Rendering&\Otto\Sapi\Http\Template\ResponderHelpers */ }}
{{ addData(['route' => $this->route()->get()]) }}
{{ response()->setCode(400) }}
