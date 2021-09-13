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
        CProfiler_Trait_PopulatePostTrait,
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
        $profilerTables[] = static::createPostTable();

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
}
