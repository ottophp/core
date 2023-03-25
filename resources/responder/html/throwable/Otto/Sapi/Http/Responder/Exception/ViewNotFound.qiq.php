{{ /** @var \Otto\Sapi\Http\Template\Template $this */ }}

<p>Could not find any of these requested views ...</p>

<pre>{{h \print_r ($e->views, true) }}</pre>

<p>... in these search paths:</p>

<pre>{{h \print_r ($e->paths, true) }}</pre>

<p>Exception:</p>

{{h $e }}
