{{ /** @var \FakeProject\Sapi\Http\Template\Template $this */ }}
{{ addData(['_status' => $this->payload()->getStatus()]) }}
{{ response()->setContent($this->getData()) }}
