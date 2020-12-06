<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Dec 6, 2020 
 * @license Ittron Global Teknologi
 */

?>
@props(['id' => null, 'maxWidth' => null])



<div {{ $attributes }}>
    <div class="cf-modal-wrapper" {{ $attributes }}>
        <div class="cf-modal-wrapper-inner">
            <!--
              Background overlay, show/hide based on modal state.
        
              Entering: "ease-out duration-300"
                From: "opacity-0"
                To: "opacity-100"
              Leaving: "ease-in duration-200"
                From: "opacity-100"
                To: "opacity-0"
            -->
            <div class="cf-modal-overlay" aria-hidden="true">
                <div class="cf-modal-overlay-inner"></div>
            </div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="cf-modal-center-helper" aria-hidden="true">&#8203;</span>
            <!--
              Modal panel, show/hide based on modal state.
        
              Entering: "ease-out duration-300"
                From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                To: "opacity-100 translate-y-0 sm:scale-100"
              Leaving: "ease-in duration-200"
                From: "opacity-100 translate-y-0 sm:scale-100"
                To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            -->
            <div class="cf-modal" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
