{{ addData(['_status' => $this->payload()?->getStatus() ?? null]) }}
{{ response()->setContent($this->getData()) }}
