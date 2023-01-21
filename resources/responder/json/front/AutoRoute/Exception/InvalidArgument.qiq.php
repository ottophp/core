{{ /** @var \Qiq\Rendering&\Otto\Sapi\Http\Responder\ResponderHelpers */ }}
{{ addData(['route' => $this->route()->get()]) }}
{{ response()->setCode(400) }}
