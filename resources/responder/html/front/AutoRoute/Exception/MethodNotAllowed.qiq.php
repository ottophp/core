{{ /** @var \Qiq\Rendering&\Otto\Sapi\Http\Responder\ResponderHelpers */ }}
<?php if ($this->request()->method->is('OPTIONS')) {
    $this->setLayout(null);
    $this->response()
        ->setCode(204)
        ->setHeader('Access-Control-Allow-Origin', '*')
        ->setHeader('Access-Control-Allow-Methods', $this->route()->headers['allowed'] ?? '-')
        ->setHeader('Access-Control-Allow-Headers', '*')
        ->setHeader('Access-Control-Allow-Credentials', 'true');
    return;
} ?>

{{
    response()
    ->setCode(405)
    ->setHeader('allowed', $this->route()->headers['allowed'] ?? '-')
}}

<p>The HTTP request method <code>{{h request()->method->name }}</code> was not allowed.</p>

<p>Router log messages:</p>

<pre>
{{h \print_r ($this->route()->messages, true) }}
</pre>

<p>Allowed methods:<p>

<pre>
{{h \print_r ($this->route()->headers['allowed'] ?? '(none)', true) }}
</pre>
