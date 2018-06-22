<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 22, 2018, 5:08:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CClientScript_Compiler {

    const TYPE_CSS = 'Css';
    const TYPE_JS = 'Js';
    const ENGINE_CSS_SCSS = 'Scss';

    protected static $instance;

    /**
     *
     * @var CClientScript_Compiler_Engine
     */
    protected $engineName;
    protected $engine;
    protected $type;

    public static function instance($type, $engineName) {
        if (!is_array(self::$instance)) {
            self::$instance = array();
        }
        if (!isset(self::$instance[$type])) {
            self::$instance[$type] = array();
        }
        if (!isset(self::$instance[$type][$engineName])) {
            self::$instance[$type][$engineName] = new CClientScript_Compiler($type, $engineName);
        }
        return self::$instance[$engineName];
    }

    public function __construct($engineName) {
        $this->type = $type;
        $this->engineName = $engineName;
        $engineClass = 'CClientScript_Compiler_Engine_' . $this->type . '_' . $this->engineName;
        $this->engine = new $engineClass();
    }

    /**
     * Compile .scss file
     *
     * @param string $in  Input path (.scss)
     * @param string $out Output path (.css)
     *
     * @return string
     */
    protected function compile($in, $out) {
        $start = microtime(true);
        $css = $this->engine->compile(file_get_contents($in), $in);
        $elapsed = round((microtime(true) - $start), 4);

        $v = $this->engine->getVersion();
        $t = @date('r');
        $css = "/* compiled by " . $engine . " " . $v . " on " . $t . " (" . $elapsed . "s) */\n\n" . $css;

        file_put_contents($out, $css);
        //file_put_contents($this->importsCacheName($out), serialize($this->scss->getParsedFiles()));
        return $css;
    }

    /**
     * Get path to cached imports
     *
     * @return string
     */
    protected function importsCacheName($out) {
        return $out . '.imports';
    }

    /**
     * Get path to cached .css file
     *
     * @return string
     */
    protected function cacheName($fname) {
        return $this->join($this->cacheDir, md5($fname) . '.css');
    }

    /**
     * Join path components
     *
     * @param string $left  Path component, left of the directory separator
     * @param string $right Path component, right of the directory separator
     *
     * @return string
     */
    protected function join($left, $right) {
        return rtrim($left, '/\\') . DIRECTORY_SEPARATOR . ltrim($right, '/\\');
    }

}
