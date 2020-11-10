<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 4:00:17 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CProfiler {

    protected static $enabled = false;

    use CProfiler_Trait_PopulateBenchmarkTrait,
        CProfiler_Trait_PopulateDatabaseTrait;

    public static function enable() {
        static::$enabled = true;
    }

    public static function disable() {
        static::$enabled = false;
    }

    public static function isEnabled() {
        return static::$enabled == true;
    }

    /**
     * Render the profiler. Output is added to the bottom of the page by default.
     *
     * @param   boolean  return the output if TRUE
     * @return  void|string
     */
    public static function render() {
        if (!static::$enabled) {
            return '';
        }
        $start = microtime(TRUE);

        $styleRenderer = new CProfiler_StyleRenderer();

        $styles = $styleRenderer->render();

        //populate data
        $profilerTables = [];
        $profilerTables[] = static::createBenchmarkTable();
        $profilerTables[] = static::createDatabaseTable();


        $html = '';
        $html.='<style type="text/css">' . $styles . '</style>';
        $html.='<div id="kohana-profiler">';
        foreach ($profilerTables as $table) {
            $html.=$table->render();
        }
        $executionTime = microtime(TRUE) - $start;
        $html.='<p class="kp-meta">Profiler executed in ' . number_format($executionTime, 3) . 's</p>';
        $html.='</div>';

        return $html;
    }

    /**
     * Session data.
     *
     * @return  void
     */
    public function session() {
        if (empty($_SESSION))
            return;

        if (!$table = $this->table('session'))
            return;

        $table->add_column('kp-name');
        $table->add_column();
        $table->add_row(array('Session', 'Value'), 'kp-title', 'background-color: #CCE8FB');

        text::alternate();
        foreach ($_SESSION as $name => $value) {
            if (is_object($value)) {
                $value = get_class($value) . ' [object]';
            }

            $data = array($name, $value);
            $class = text::alternate('', 'kp-altrow');
            $table->add_row($data, $class);
        }
    }

    /**
     * POST data.
     *
     * @return  void
     */
    public function post() {
        if (empty($_POST))
            return;

        if (!$table = $this->table('post'))
            return;

        $table->add_column('kp-name');
        $table->add_column();
        $table->add_row(array('POST', 'Value'), 'kp-title', 'background-color: #E0E0FF');

        text::alternate();
        foreach ($_POST as $name => $value) {
            $data = array($name, $value);
            $class = text::alternate('', 'kp-altrow');
            $table->add_row($data, $class);
        }
    }

    /**
     * Cookie data.
     *
     * @return  void
     */
    public function cookies() {
        if (empty($_COOKIE))
            return;

        if (!$table = $this->table('cookies'))
            return;

        $table->add_column('kp-name');
        $table->add_column();
        $table->add_row(array('Cookies', 'Value'), 'kp-title', 'background-color: #FFF4D7');

        text::alternate();
        foreach ($_COOKIE as $name => $value) {
            $data = array($name, $value);
            $class = text::alternate('', 'kp-altrow');
            $table->add_row($data, $class);
        }
    }

}
