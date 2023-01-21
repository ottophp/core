{{ /** @var \Qiq\Rendering&\Otto\Sapi\Http\Responder\ResponderHelpers */ }}
{{ response()->setHeader('content-type', 'text/html') }}
<html>
<head>
    <title>Welcome To Otto</title>
</head>
<body>
{{= getContent() }}
</body>
</html>
