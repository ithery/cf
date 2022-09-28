<div class="cres-editor-js-block">
    <div class="cres-editor-js-image {{ $classes }} {{ isset($alignment) ? 'text-'.$alignment : 'text-left' }}">
        <img src="{{ $file['url'] }}" alt="{{ $caption }}">
        @if (!empty($caption))
            <div class="cres-editor-js-image-caption">{{ $caption }}</div>
        @endif
    </div>
</div>
