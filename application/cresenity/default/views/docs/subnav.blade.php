<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Dec 5, 2020 
 * @license Ittron Global Teknologi
 */
?>
<div class="sidebar-submenu">
    <ul>
        @foreach($subnavs as $subnav)
        <li> <a class="active" href="{{c::url(carr::get($subnav,'uri'))}}">{{carr::get($subnav,'label')}}</a></li>

        @endforeach
    </ul>
</div>

