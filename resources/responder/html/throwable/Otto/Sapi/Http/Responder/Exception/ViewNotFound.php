{{ extract ($this->e->info) }}

<p>The <code>{{h $class}}</code> method <code>{{h $method}}()</code> could not
find any of these requested views ...</p>

<pre>{{h print_r ($views, true) }}</pre>

<p>... in these search paths:</p>

<pre>{{h print_r ($paths, true) }}</pre>

<p>Exception:</p>

{{h $this->e }}
