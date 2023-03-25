{{ /** @var \Otto\Sapi\Http\Template\Template $this */ }}
{{ response()->setHeader('content-type', 'text/html') }}
<html>
<head>
    <title>Welcome To Otto</title>
</head>
<body>
{{= getContent() }}
</body>
</html>
