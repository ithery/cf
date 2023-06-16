<?php

use DebugBar\DataCollector\Renderable;

/**
 * Collects info about PHP.
 */
class CDebug_DataCollector_PhpInfoCollector extends CDebug_DataCollector implements Renderable {
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
