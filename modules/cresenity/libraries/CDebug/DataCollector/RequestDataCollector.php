<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 5:20:27 PM
 */

/**
 * Collects info about the current request
 */
class CDebug_DataCollector_RequestDataCollector extends CDebug_DataCollector implements CDebug_Bar_Interface_RenderableInterface, CDebug_DataCollector_AssetProviderInterface {
    // The HTML var dumper requires debug bar users to support the new inline assets, which not all
    // may support yet - so return false by default for now.
    protected $useHtmlVarDumper = true;

    /**
     * Sets a flag indicating whether the Symfony HtmlDumper will be used to dump variables for
     * rich variable rendering.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function useHtmlVarDumper($value = true) {
        $this->useHtmlVarDumper = $value;
        return $this;
    }

    /**
     * Indicates whether the Symfony HtmlDumper will be used to dump variables for rich variable
     * rendering.
     *
     * @return mixed
     */
    public function isHtmlVarDumperUsed() {
        return $this->useHtmlVarDumper;
    }

    /**
     * @return array
     */
    public function collect() {
        $vars = ['_GET', '_POST', '_SESSION', '_COOKIE', '_SERVER'];
        $data = [];
        foreach ($vars as $var) {
            if (isset($GLOBALS[$var])) {
                $key = '$' . $var;
                if ($this->isHtmlVarDumperUsed()) {
                    $data[$key] = $this->getVarDumper()->renderVar($GLOBALS[$var]);
                } else {
                    $data[$key] = $this->getDataFormatter()->formatVar($GLOBALS[$var]);
                }
            }
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'request';
    }

    /**
     * @return array
     */
    public function getAssets() {
        return $this->isHtmlVarDumperUsed() ? $this->getVarDumper()->getAssets() : [];
    }

    /**
     * @return array
     */
    public function getWidgets() {
        $widget = $this->isHtmlVarDumperUsed() ? 'PhpDebugBar.Widgets.HtmlVariableListWidget' : 'PhpDebugBar.Widgets.VariableListWidget';
        return [
            'request' => [
                'icon' => 'tags',
                'widget' => $widget,
                'map' => 'request',
                'default' => '{}'
            ]
        ];
    }
}
