<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Dec 5, 2020 
 * @license Ittron Global Teknologi
 */
?>
<ul>
@foreach($navs as $nav)
<li class="sidebar-dropdown">
    <a href="javascript:;">  <span class="menu-text">{{ carr::get($nav,'label') }}</span></a>
    @if(carr::get($nav,'subnav')!=null)
        @include('docs.subnav',['subnavs'=>carr::get($nav,'subnav')])
    @endif
</li>
@endforeach
</ul>