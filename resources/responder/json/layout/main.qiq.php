{{ /** @var \Otto\Sapi\Http\Template\Template $this */ }}
{{ response()->setJson(
    value: getData(),
    type: 'application/json',
    flags: JSON_PRETTY_PRINT
        | JSON_INVALID_UTF8_SUBSTITUTE
        | JSON_PRESERVE_ZERO_FRACTION
        | JSON_THROW_ON_ERROR
        | JSON_UNESCAPED_SLASHES
        | JSON_UNESCAPED_UNICODE
) }}
