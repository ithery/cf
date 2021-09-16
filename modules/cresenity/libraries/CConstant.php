<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 4:20:26 PM
 */
class CConstant implements CApp_Interface_ConstantInterface {
    use CApp_Trait_ConstantTrait;

    /**
     * Handler Constant
     */
    const HANDLER_TYPE_DIALOG = CObservable_Listener_Handler::TYPE_DIALOG;
    const HANDLER_TYPE_REMOVE = CObservable_Listener_Handler::TYPE_REMOVE;
    const HANDLER_TYPE_SUBMIT = CObservable_Listener_Handler::TYPE_SUBMIT;
    const HANDLER_TYPE_EMPTY = CObservable_Listener_Handler::TYPE_EMPTY;
    const HANDLER_TYPE_CUSTOM = CObservable_Listener_Handler::TYPE_CUSTOM;
    const HANDLER_TYPE_APPEND = CObservable_Listener_Handler::TYPE_APPEND;
    const HANDLER_TYPE_RELOAD = CObservable_Listener_Handler::TYPE_RELOAD;

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

    /**
     * Table View Constant
     */
    const TABLE_VIEW_COL = 'col';
    const TABLE_VIEW_ROW = 'row';
}
