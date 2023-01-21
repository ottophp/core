{{ /** @var \Qiq\Rendering&\Otto\Sapi\Http\Template\ResponderHelpers */ }}

<p>Could not find any of these requested views ...</p>

<pre>{{h \print_r ($e->views, true) }}</pre>

<p>... in these search paths:</p>

<pre>{{h \print_r ($e->paths, true) }}</pre>

<p>Exception:</p>

{{h $e }}
