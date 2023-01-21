{{ /** @var \Qiq\Rendering&\Otto\Sapi\Http\Template\ResponderHelpers */ }}
{{ response()->setHeader('content-type', 'text/html') }}
<html>
<head>
    <title>Welcome To Otto</title>
</head>
<body>
{{= getContent() }}
</body>
</html>
