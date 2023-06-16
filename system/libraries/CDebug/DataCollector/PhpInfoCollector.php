<?php

use DebugBar\DataCollector\Renderable;

use DebugBar\DataCollector\DataCollector;

/**
 * Collects info about PHP.
 */
class CDebug_DataCollector_PhpInfoCollector extends DataCollector implements Renderable {
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
