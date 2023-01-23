{{ /** @var \Otto\Sapi\Http\Template\Template $this */ }}

{{ response()->setCode(400) }}

<p>The request was bad.</p>

<p>Router log messages:</p>

<pre>
{{h \print_r ($this->route()->messages, true) }}
</pre>
