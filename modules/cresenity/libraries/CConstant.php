<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 4:20:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CConstant {

    const HANDLER_DIALOG = CRenderable_Listener_Handler::TYPE_DIALOG;
    const HANDLER_REMOVE = CRenderable_Listener_Handler::TYPE_REMOVE;
    const HANDLER_SUBMIT = CRenderable_Listener_Handler::TYPE_SUBMIT;
    const HANDLER_EMPTY = CRenderable_Listener_Handler::TYPE_EMPTY;
    const HANDLER_CUSTOM = CRenderable_Listener_Handler::TYPE_CUSTOM;
    const HANDLER_APPEND = CRenderable_Listener_Handler::TYPE_APPEND;
    const HANDLER_RELOAD = CRenderable_Listener_Handler::TYPE_RELOAD;

}
