{{ /** @var \Qiq\Rendering&\Otto\Sapi\Http\Template\ResponderHelpers */ }}
{{ response()->setJson(
    value: $this->getData(),
    type: 'application/json',
    flags: JSON_PRETTY_PRINT
        | JSON_INVALID_UTF8_SUBSTITUTE
        | JSON_PRESERVE_ZERO_FRACTION
        | JSON_THROW_ON_ERROR
        | JSON_UNESCAPED_SLASHES
        | JSON_UNESCAPED_UNICODE
) }}
