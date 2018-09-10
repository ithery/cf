<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 5:08:47 PM
 * @license Ittron Global Teknologi <ittron.co.id>
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
        return setlocale(LC_ALL, 0);
    }

    /**
     * Get the current translations domain
     *
     * @return string
     */
    public function getDomain() {
        return textdomain(null);
    }

    /**
     * @return array
     */
    public function collect() {
        return array(
            'locale' => $this->getLocale(),
            'domain' => $this->getDomain(),
        );
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
        return array(
            'domain' => array(
                'icon' => 'bookmark',
                'map' => 'localization.domain',
            ),
            'locale' => array(
                'icon' => 'flag',
                'map' => 'localization.locale',
            )
        );
    }

}
