<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 5:08:47 PM
 */

/**
 * Collects info about the current localization state
 */
class CDebug_DataCollector_LocalizationCollector extends CDebug_DataCollector implements CDebug_Bar_Interface_RenderableInterface {
    /**
     * Get the current locale
     *
     * @return string
     */
    public function getLocale() {
        return CF::getLocale();
    }

    /**
     * Get the current domain
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
