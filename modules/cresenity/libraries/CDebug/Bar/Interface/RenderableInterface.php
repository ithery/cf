<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 2:38:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Indicates that a DataCollector is renderable using Renderer
 */
interface CDebug_Bar_Interface_RenderableInterface {

    /**
     * Returns a hash where keys are control names and their values
     * an array of options as defined in {@see DebugBar\JavascriptRenderer::addControl()}
     *
     * @return array
     */
    function getWidgets();
}
