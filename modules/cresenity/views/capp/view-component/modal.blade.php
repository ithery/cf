<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Dec 6, 2020 
 * @license Ittron Global Teknologi
 */
?>
@props(['id' => null, 'maxWidth' => null])

<div :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-3 py-2">
        <div class="text-lg">
            {{ $title }}
        </div>

        <div class="mt-2">
            {{ $content }}
        </div>
    </div>

    <div class="px-3 py-2 bg-gray text-right">
        {{ $footer }}
    </div>
</div>