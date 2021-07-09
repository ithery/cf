<div>
    {{ $category }}
</div>
<ul>
    @foreach($navs as $nav)
    <li class="sidebar-dropdown {{ carr::get($nav,'name')==$category ? ' active':'' }}">
        <a href="javascript:;"> <span class="menu-text">{{ carr::get($nav,'label') }}</span></a>
        @if(carr::get($nav,'subnav')!=null)
            @include('docs.subnav',['subnavs'=>carr::get($nav,'subnav')])
        @endif
    </li>
    @endforeach
</ul>
