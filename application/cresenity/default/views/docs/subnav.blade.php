<?php
defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Dec 5, 2020
 */
?>
<div class="sidebar-submenu">
    <ul>
        @foreach($subnavs as $subnav)
        <li> <a class="menu-{{ carr::get($subnav,'name') }} {{cstr::endsWith(carr::get($subnav,'name'), $page) ? 'active' : ''}}" href="{{c::url(carr::get($subnav,'uri'))}}">{{carr::get($subnav,'label')}}</a></li>

        @endforeach
    </ul>
</div>
