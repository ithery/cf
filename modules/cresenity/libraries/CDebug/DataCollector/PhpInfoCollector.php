<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 2:38:06 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Collects info about PHP
 */
class CDebug_DataCollector_PhpInfoCollector extends CDebug_DataCollector implements CDebug_Bar_Interface_RenderableInterface {

    /**
     * @return string
     */
    public function getName() {
        return 'php';
    }

    /**
     * @return array
     */
    public function collect() {
        return array(
            'version' => PHP_VERSION,
            'interface' => PHP_SAPI
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets() {
        return array(
            "php_version" => array(
                "icon" => "code",
                "tooltip" => "Version",
                "map" => "php.version",
                "default" => ""
            ),
        );
    }

}
