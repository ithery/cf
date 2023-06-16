<?php

defined('SYSPATH') or die('No direct access allowed.');
use DebugBar\DataCollector\Renderable;

use DebugBar\DataCollector\DataCollector;

/**
 * Collects info about the current localization state.
 */
class CDebug_DataCollector_LocalizationCollector extends DataCollector implements Renderable {
    /**
     * Get the current locale.
     *
     * @return string
     */
    public function getLocale() {
        return CF::getLocale();
    }

    /**
     * Get the current domain.
     *
     * @return string
     */
    public function getDomain() {
        return CF::domain();
    }

    /**
     * @return array
     */
    public function collect() {
        return [
            'locale' => $this->getLocale(),
            'domain' => $this->getDomain(),
        ];
    }

    /**
     * @return string
     */
    public function getName() {
        return 'localization';
    }

    /**
     * @return array
     */
    public function getWidgets() {
        return [
            /*
            'domain' => [
                'icon' => 'bookmark',
                'map' => 'localization.domain',
                'tooltip' => 'Current domain',
                'default' => ''
            ],
            */
            'locale' => [
                'icon' => 'flag',
                'map' => 'localization.locale',
                'tooltip' => 'Current locale',
                'default' => ''
            ]
        ];
    }
}
