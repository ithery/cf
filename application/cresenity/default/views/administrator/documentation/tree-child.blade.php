<ul>
    @foreach($childs as $child)
    <li>
        {{ carr::get($child,'title') }}
        @if(count(carr::get($child,'childs',[])))
            @include('administrator.documentation.tree-child',['childs' => carr::get($child,'childs',[])])
         @endif
    </li>
    @endforeach
</ul>