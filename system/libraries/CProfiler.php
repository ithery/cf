<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 4:00:17 PM
 */
class CProfiler {
    protected static $enabled = false;

    use CProfiler_Trait_PopulateBenchmarkTrait,
        CProfiler_Trait_PopulateCookiesTrait,
        CProfiler_Trait_PopulateSessionTrait,
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
     * @param   bool  return the output if TRUE
     *
     * @return void|string
     */
    public static function render() {
        if (!static::$enabled) {
            return '';
        }
        $start = microtime(true);

        $styleRenderer = new CProfiler_StyleRenderer();

        $styles = $styleRenderer->render();

        //populate data
        $profilerTables = [];
        $profilerTables[] = static::createBenchmarkTable();
        $profilerTables[] = static::createDatabaseTable();
        $profilerTables[] = static::createCookiesTable();
        $profilerTables[] = static::createSessionTable();

        $html = '';
        $html .= '<style type="text/css">' . $styles . '</style>';
        $html .= '<a href="javascript:;" id="cf-profiler-button">Profiler</a>';
        $html .= '<div id="cf-profiler">';
        foreach ($profilerTables as $table) {
            $html .= $table->render();
        }
        $executionTime = microtime(true) - $start;
        $html .= '<p class="cp-meta">Profiler executed in ' . number_format($executionTime, 3) . 's</p>';
        $html .= '</div>';

        $js = '';
        $js = "
            <script>
            document.getElementById('cf-profiler-button').addEventListener('click', e => {
                document.getElementById('cf-profiler').classList.toggle('active');
            });
            </script>


        ";

        return $html . $js;
    }

    /**
     * Session data.
     *
     * @return void
     */
    public function session() {
        if (empty($_SESSION)) {
            return;
        }

        if (!$table = $this->table('session')) {
            return;
        }

        $table->add_column('kp-name');
        $table->add_column();
        $table->add_row(['Session', 'Value'], 'kp-title', 'background-color: #CCE8FB');

        text::alternate();
        foreach ($_SESSION as $name => $value) {
            if (is_object($value)) {
                $value = get_class($value) . ' [object]';
            }

            $data = [$name, $value];
            $class = text::alternate('', 'kp-altrow');
            $table->add_row($data, $class);
        }
    }

    /**
     * POST data.
     *
     * @return void
     */
    public function post() {
        if (empty($_POST)) {
            return;
        }

        if (!$table = $this->table('post')) {
            return;
        }

        $table->add_column('kp-name');
        $table->add_column();
        $table->add_row(['POST', 'Value'], 'kp-title', 'background-color: #E0E0FF');

        text::alternate();
        foreach ($_POST as $name => $value) {
            $data = [$name, $value];
            $class = text::alternate('', 'kp-altrow');
            $table->add_row($data, $class);
        }
    }
}
