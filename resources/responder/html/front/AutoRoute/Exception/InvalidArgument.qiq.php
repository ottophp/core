{{ /** @var \Qiq\Rendering&\Otto\Sapi\Http\Responder\ResponderHelpers */ }}

{{ response()->setCode(400) }}

<p>The request was bad.</p>

<p>Router log messages:</p>

<pre>
{{h \print_r ($this->route()->messages, true) }}
</pre>
