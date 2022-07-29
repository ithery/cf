<div class="cres-editor-js-block">
    <div class="cres-editor-js-link">
        <a href="{{ $link }}" class="cres-editor-js-link-content">
            @if (!empty($meta['image']['url']))
                <div class="cres-editor-js-link-image" style="background-image: url('{{ $meta['image']['url'] }}');"></div>
            @endif

            <h4>{{ $meta['title'] }}</h4>
            <p>
                {{ $meta['description'] }}
            </p>
            <p>
                <small>{{ $link }}</small>
            </p>
        </a>
    </div>
</div>
