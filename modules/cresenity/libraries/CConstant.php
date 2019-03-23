<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 4:20:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CConstant {

    /**
     * Handler Constant
     */
    const HANDLER_TYPE_DIALOG = CRenderable_Listener_Handler::TYPE_DIALOG;
    const HANDLER_TYPE_REMOVE = CRenderable_Listener_Handler::TYPE_REMOVE;
    const HANDLER_TYPE_SUBMIT = CRenderable_Listener_Handler::TYPE_SUBMIT;
    const HANDLER_TYPE_EMPTY = CRenderable_Listener_Handler::TYPE_EMPTY;
    const HANDLER_TYPE_CUSTOM = CRenderable_Listener_Handler::TYPE_CUSTOM;
    const HANDLER_TYPE_APPEND = CRenderable_Listener_Handler::TYPE_APPEND;
    const HANDLER_TYPE_RELOAD = CRenderable_Listener_Handler::TYPE_RELOAD;

    /**
     * Path Constant
     */
    const CRESENITY_PATH = DOCROOT . '/modules/cresenity';
    const CRESENITY_MEDIA_PATH = DOCROOT . '/modules/cresenity/media';
    const CRESENITY_FONT_PATH = DOCROOT . '/modules/cresenity/media/font';
    const CRESENITY_IMAGE_PATH = DOCROOT . '/modules/cresenity/media/img';

    /**
     * Align Constant
     */
    const ALIGN_LEFT = 'left';
    const ALIGN_RIGHT = 'right';
    const ALIGN_CENTER = 'center';

}
