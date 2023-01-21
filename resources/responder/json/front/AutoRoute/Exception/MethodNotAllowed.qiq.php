{{ /** @var \Qiq\Rendering&\Otto\Sapi\Http\Responder\ResponderHelpers */ }}
<?php
if ($this->request()->method->is('OPTIONS')) {
    $this->setLayout(null);
    $this->response()
        ->setCode(204)
        ->setHeader('Access-Control-Allow-Origin', '*')
        ->setHeader('Access-Control-Allow-Methods', $this->route()->headers['allowed'] ?? '-')
        ->setHeader('Access-Control-Allow-Headers', '*')
        ->setHeader('Access-Control-Allow-Credentials', 'true');
    return;
}

$this->addData(['route' => $this->route()->get()]);
$this->response()
    ->setCode(405)
    ->setHeader('allowed', $this->route()->headers['allowed'] ?? '-');
