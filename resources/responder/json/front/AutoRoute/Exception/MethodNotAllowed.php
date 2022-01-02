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
{{ $this->route = $this->route() }}
{{ $this->e = $this->decomposeException($this->route->exception) }}
{{
    response()
    ->setCode(405)
    ->setHeader('allowed', $this->route()->headers['allowed'] ?? '-')
}}
