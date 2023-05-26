<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Collects info about PHP.
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
        return [
            'version' => PHP_VERSION,
            'interface' => PHP_SAPI
        ];
    }

    /**
     * @inheritDoc
     */
    public function getWidgets() {
        return [
            'php_version' => [
                'icon' => 'code',
                'tooltip' => 'PHP Version',
                'map' => 'php.version',
                'default' => ''
            ],
        ];
    }
}
