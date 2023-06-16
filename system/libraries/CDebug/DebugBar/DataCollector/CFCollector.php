<?php

use DebugBar\DataCollector\Renderable;
use DebugBar\DataCollector\DataCollector;

class CDebug_DebugBar_DataCollector_CFCollector extends DataCollector implements Renderable {
    /**
     * @inheritDoc
     */
    public function collect() {
        return [
            'version' => CF::version(),
            'environment' => CF::environment(),
            'locale' => CF::getLocale(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getName() {
        return 'cf';
    }

    /**
     * @inheritDoc
     */
    public function getWidgets() {
        return [
            'version' => [
                'icon' => 'github',
                'tooltip' => 'CF Version',
                'map' => 'cf.version',
                'default' => ''
            ],
            'environment' => [
                'icon' => 'desktop',
                'tooltip' => 'Environment',
                'map' => 'cf.environment',
                'default' => ''
            ],
            'locale' => [
                'icon' => 'flag',
                'tooltip' => 'Current locale',
                'map' => 'cf.locale',
                'default' => '',
            ],
        ];
    }
}
