{{ /** @var \Qiq\Rendering&\Otto\Sapi\Http\Template\ResponderHelpers */ }}

{{ response()->setCode(404) }}

<p>Route not found for URL.</p>

<p>Router log messages:</p>

<pre>
{{h \print_r ($this->route()->messages, true) }}
</pre>
