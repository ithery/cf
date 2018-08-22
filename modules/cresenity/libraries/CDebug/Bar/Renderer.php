<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 1:20:44 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDebug_Bar_Renderer {

    /**
     *
     * @var CDebug_Bar
     */
    protected $debugBar;

    /**
     *
     * @var string
     */
    protected $javascriptClass = 'PhpDebugBar.DebugBar';

    /**
     *
     * @var string
     */
    protected $variableName = 'phpdebugbar';
    protected $controls = array();
    protected $ignoredCollectors = array();

    const REPLACEABLE_TAG = '<!-- CAPP-DEBUGBAR-CODE -->';
    const REPLACEABLE_JS_TAG = '/* CAPP-DEBUGBAR-CODE */';

    public function __construct(CDebug_Bar $bar) {
        $this->debugBar = $bar;
    }

    public function populateAssets() {
        $clientScript = CClientScript::instance();
        $clientScript->registerCssFile('debug/debugbar.css');
        $clientScript->registerCssFile('debug/debugbar/widgets.css');
        $clientScript->registerJsFile('debug/debugbar.js');
        $clientScript->registerJsFile('debug/debugbar/widgets.js');
    }

    public function getJavascriptCode() {
        $initCode = sprintf("var %s = new %s();\n", $this->variableName, $this->javascriptClass);
        $definitionCode = $this->getJsControlsDefinitionCode($this->variableName);
        return $initCode . $definitionCode;
    }

    public function getJavascriptReplaceCode() {
        return self::REPLACEABLE_JS_TAG;
    }

    public function replaceJavascriptCode($string) {
        return str_replace($this->getJavascriptReplaceCode(), $this->getJavascriptCode(), $string);
    }

    /**
     * Returns the js code needed to initialized the controls and data mapping of the debug bar
     *
     * Controls can be defined by collectors themselves or using {@see addControl()}
     *
     * @param string $varname Debug bar's variable name
     * @return string
     */
    protected function getJsControlsDefinitionCode($varname) {
        $js = '';
        $dataMap = array();
        $excludedOptions = array('indicator', 'tab', 'map', 'default', 'widget', 'position');
        // finds controls provided by collectors
        $widgets = array();
        foreach ($this->debugBar->getCollectors() as $collector) {
            if (($collector instanceof CDebug_Bar_Interface_RenderableInterface) && !in_array($collector->getName(), $this->ignoredCollectors)) {
                if ($w = $collector->getWidgets()) {
                    $widgets = array_merge($widgets, $w);
                }
            }
        }

        $controls = array_merge($widgets, $this->controls);

        foreach (array_filter($controls) as $name => $options) {
            $opts = array_diff_key($options, array_flip($excludedOptions));
            if (isset($options['tab']) || isset($options['widget'])) {
                if (!isset($opts['title'])) {
                    $opts['title'] = ucfirst(str_replace('_', ' ', $name));
                }
                $js .= sprintf("%s.addTab(\"%s\", new %s({%s%s}));\n", $varname, $name, isset($options['tab']) ? $options['tab'] : 'PhpDebugBar.DebugBar.Tab', substr(json_encode($opts, JSON_FORCE_OBJECT), 1, -1), isset($options['widget']) ? sprintf('%s"widget": new %s()', count($opts) ? ', ' : '', $options['widget']) : ''
                );
            } elseif (isset($options['indicator']) || isset($options['icon'])) {
                $js .= sprintf("%s.addIndicator(\"%s\", new %s(%s), \"%s\");\n", $varname, $name, isset($options['indicator']) ? $options['indicator'] : 'PhpDebugBar.DebugBar.Indicator', json_encode($opts, JSON_FORCE_OBJECT), isset($options['position']) ? $options['position'] : 'right'
                );
            }
            if (isset($options['map']) && isset($options['default'])) {
                $dataMap[$name] = array($options['map'], $options['default']);
            }
        }
        // creates the data mapping object
        $mapJson = array();
        foreach ($dataMap as $name => $values) {
            $mapJson[] = sprintf('"%s": ["%s", %s]', $name, $values[0], $values[1]);
        }
        $js .= sprintf("%s.setDataMap({\n%s\n});\n", $varname, implode(",\n", $mapJson));
        // activate state restoration
        $js .= sprintf("%s.restoreState();\n", $varname);
        return $js;
    }

    public function apply() {
        CFEvent::add('system.display', function() {
            CF::$output = $this->replaceJavascriptCode(CF::$output);
        });
    }

}
