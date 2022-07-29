<div class="cres-editor-js-block">
    <div class="cres-editor-js-image {{ $classes }}">
        <img src="{{ $file['url'] }}" alt="{{ $caption }}">
        @if (!empty($caption))
            <caption>{{ $caption }}</caption>
        @endif
    </div>
</div>
