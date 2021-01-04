<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 6:21:24 PM
 */

/**
 * Collects data about SQL statements executed with PDO
 */
class CDebug_DataCollector_QueryCollector extends CDebug_DataCollector implements CDebug_Bar_Interface_RenderableInterface, CDebug_DataCollector_AssetProviderInterface {
    protected $timeCollector;
    protected $queries = [];
    protected $renderSqlWithParams = false;
    protected $findSource = false;
    protected $middleware = [];
    protected $explainQuery = false;
    protected $explainTypes = ['SELECT']; // ['SELECT', 'INSERT', 'UPDATE', 'DELETE']; for MySQL 5.6.3+
    protected $showHints = true;
    protected $reflection = [];
    protected $showCopyButton = true;

    /**
     * @param CDebug_DataCollector_TimeDataCollector $timeCollector
     */
    public function __construct(CDebug_DataCollector_TimeDataCollector $timeCollector = null) {
        $this->timeCollector = $timeCollector;
        $this->setDataFormatter(new CDebug_DataFormatter_QueryFormatter());
        $db = CDatabase::instance();
        try {
            $db->listenOnQueryExecuted(
                function ($query) use ($db) {
                    $bindings = $query->bindings;
                    $time = $query->time;
                    $connection = $query->connection;
                    $sql = $query->sql;
                    $this->addQuery($sql, $bindings, $time, $connection);
                }
            );
        } catch (\Exception $e) {
            CDebug::bar()->addThrowable(
                new Exception(
                    'Cannot add listen to Queries for Debugbar: ' . $e->getMessage(),
                    $e->getCode(),
                    $e
                )
            );
        }
    }

    /**
     * Renders the SQL of traced statements with params embedded
     *
     * @param boolean $enabled
     * @param string  $quotationChar NOT USED
     */
    public function setRenderSqlWithParams($enabled = true, $quotationChar = "'") {
        $this->renderSqlWithParams = $enabled;
    }

    /**
     * Show or hide the hints in the parameters
     *
     * @param boolean $enabled
     */
    public function setShowHints($enabled = true) {
        $this->showHints = $enabled;
    }

    /**
     * Enable/disable finding the source
     *
     * @param bool  $value
     * @param array $middleware
     */
    public function setFindSource($value, array $middleware) {
        $this->findSource = (bool) $value;
        $this->middleware = $middleware;
    }

    /**
     * Enable/disable the EXPLAIN queries
     *
     * @param bool       $enabled
     * @param array|null $types   Array of types to explain queries (select/insert/update/delete)
     */
    public function setExplainSource($enabled, $types) {
        $this->explainQuery = $enabled;
        if ($types) {
            $this->explainTypes = $types;
        }
    }

    /**
     * @param string    $query
     * @param array     $bindings
     * @param float     $time
     * @param CDatabase $db
     */
    public function addQuery($query, $bindings, $time, $db) {
        $explainResults = [];
        $time = $time / 1000;
        $endTime = microtime(true);
        $startTime = $endTime - $time;
        $hints = $this->performQueryAnalysis($query);
        $bindings = $db->prepareBindings($bindings);
        // Run EXPLAIN on this query (if needed)
        if ($this->explainQuery && preg_match('/^(' . implode($this->explainTypes) . ') /i', $query)) {
            $result = $db->query('EXPLAIN ' . $query, $bindings);

            //$statement->execute($bindings);
            $explainResults = $result->result_array();
        }
        $bindings = $this->getDataFormatter()->checkBindings($bindings);
        if (!empty($bindings) && $this->renderSqlWithParams) {
            foreach ($bindings as $key => $binding) {
                // This regex matches placeholders only, not the question marks,
                // nested in quotes, while we iterate through the bindings
                // and substitute placeholders by suitable values.
                $regex = is_numeric($key) ? "/\?(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/" : "/:{$key}(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/";
                $query = preg_replace($regex, $db->escape($binding), $query, 1);
            }
        }
        $source = [];
        try {
            $source = $this->findSource();
        } catch (\Exception $e) {
        }
        $this->queries[] = [
            'query' => $query,
            'type' => 'query',
            'bindings' => $this->getDataFormatter()->escapeBindings($bindings),
            'time' => $time,
            'source' => $source,
            'explain' => $explainResults,
            'connection' => $db->getDatabaseName(),
            'hints' => $this->showHints ? $hints : null,
            'show_copy' => $this->showCopyButton,
        ];
        if ($this->timeCollector !== null) {
            $this->timeCollector->addMeasure($query, $startTime, $endTime);
        }
    }

    /**
     * Explainer::performQueryAnalysis()
     *
     * Perform simple regex analysis on the code
     *
     * @param string $query
     *
     * @package xplain (https://github.com/rap2hpoutre/mysql-xplain-xplain)
     *
     * @author e-doceo
     * @copyright 2014
     *
     * @version $Id$
     * @access public
     *
     * @return string
     */
    protected function performQueryAnalysis($query) {
        // @codingStandardsIgnoreStart
        $hints = [];
        if (preg_match('/^\\s*SELECT\\s*`?[a-zA-Z0-9]*`?\\.?\\*/i', $query)) {
            $hints[] = 'Use <code>SELECT *</code> only if you need all columns from table';
        }
        if (preg_match('/ORDER BY RAND()/i', $query)) {
            $hints[] = '<code>ORDER BY RAND()</code> is slow, try to avoid if you can.
                You can <a href="http://stackoverflow.com/questions/2663710/how-does-mysqls-order-by-rand-work" target="_blank">read this</a>
                or <a href="http://stackoverflow.com/questions/1244555/how-can-i-optimize-mysqls-order-by-rand-function" target="_blank">this</a>';
        }
        if (strpos($query, '!=') !== false) {
            $hints[] = 'The <code>!=</code> operator is not standard. Use the <code>&lt;&gt;</code> operator to test for inequality instead.';
        }
        if (stripos($query, 'WHERE') === false && preg_match('/^(SELECT) /i', $query)) {
            $hints[] = 'The <code>SELECT</code> statement has no <code>WHERE</code> clause and could examine many more rows than intended';
        }
        if (preg_match('/LIMIT\\s/i', $query) && stripos($query, 'ORDER BY') === false) {
            $hints[] = '<code>LIMIT</code> without <code>ORDER BY</code> causes non-deterministic results, depending on the query execution plan';
        }
        if (preg_match('/LIKE\\s[\'"](%.*?)[\'"]/i', $query, $matches)) {
            $hints[] = 'An argument has a leading wildcard character: <code>' . $matches[1] . '</code>.
                The predicate with this argument is not sargable and cannot use an index if one exists.';
        }
        return $hints;

        // @codingStandardsIgnoreEnd
    }

    /**
     * Use a backtrace to search for the origins of the query.
     *
     * @return array
     */
    protected function findSource() {
        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, 50);

        $sources = [];

        foreach ($stack as $index => $trace) {
            $sources[] = $this->parseTrace($index, $trace);
        }

        return array_filter($sources);
    }

    /**
     * Parse a trace element from the backtrace stack.
     *
     * @param int   $index
     * @param array $trace
     *
     * @return object|bool
     */
    protected function parseTrace($index, array $trace) {
        $frame = (object) [
            'index' => $index,
            'namespace' => null,
            'name' => null,
            'line' => isset($trace['line']) ? $trace['line'] : '?',
        ];
        if (isset($trace['function']) && $trace['function'] == 'substituteBindings') {
            $frame->name = 'Route binding';
            return $frame;
        }
        if (isset($trace['class'])
            && isset($trace['file'])
            && !$this->fileIsInExcludedPath($trace['file'])
        ) {
            $file = $trace['file'];

            $frame->name = $this->normalizeFilename($file);
            return $frame;
        }
        return false;
    }

    /**
     * Collect a database transaction event.
     *
     * @param string                          $event
     * @param \Illuminate\Database\Connection $connection
     *
     * @return array
     */
    public function collectTransactionEvent($event, $connection) {
        $source = [];

        if ($this->findSource) {
            try {
                $source = $this->findSource();
            } catch (\Exception $e) {
            }
        }

        $this->queries[] = [
            'query' => $event,
            'type' => 'transaction',
            'bindings' => [],
            'time' => 0,
            'source' => $source,
            'explain' => [],
            'connection' => $connection->getDatabaseName(),
            'hints' => null,
            'show_copy' => false,
        ];
    }

    /**
     * Reset the queries.
     */
    public function reset() {
        $this->queries = [];
    }

    /**
     * {@inheritDoc}
     */
    public function collect() {
        $totalTime = 0;

        $queries = $this->queries;
        $statements = [];
        foreach ($queries as $query) {
            $totalTime += $query['time'];
            $statements[] = [
                'sql' => $this->getDataFormatter()->formatSql($query['query']),
                'type' => $query['type'],
                'params' => [],
                'bindings' => $query['bindings'],
                'hints' => $query['hints'],
                'backtrace' => array_values($query['source']),
                'duration' => $query['time'],
                'duration_str' => ($query['type'] == 'transaction') ? '' : $this->formatDuration($query['time']),
                'stmt_id' => $this->getDataFormatter()->formatSource(reset($query['source'])),
                'connection' => $query['connection'],
            ];
            //Add the results from the explain as new rows
            foreach ($query['explain'] as $explain) {
                $statements[] = [
                    'sql' => ' - EXPLAIN #' . $explain->id . ': `' . $explain->table . '` (' . $explain->select_type . ')',
                    'type' => 'explain',
                    'params' => $explain,
                    'row_count' => $explain->rows,
                    'stmt_id' => $explain->id,
                ];
            }
        }
        $nb_statements = array_filter($queries, function ($query) {
            return $query['type'] == 'query';
        });
        $data = [
            'nb_statements' => count($nb_statements),
            'nb_failed_statements' => 0,
            'accumulated_duration' => $totalTime,
            'accumulated_duration_str' => $this->formatDuration($totalTime),
            'statements' => $statements
        ];
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getName() {
        return 'queries';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets() {
        return [
            'queries' => [
                'icon' => 'database',
                'widget' => 'PhpDebugBar.Widgets.CFSQLQueriesWidget',
                'map' => 'queries',
                'default' => '[]'
            ],
            'queries:badge' => [
                'map' => 'queries.nb_statements',
                'default' => 0
            ]
        ];
    }

    public function getAssets() {
        return [
            'css' => ['debug/debugbar/widgets/sqlqueries/widget.css'],
            'js' => ['debug/debugbar/widgets/cfsqlqueries/widget.js']
        ];
    }

    /**
     * @return CDebug_DataFormatter_QueryFormatter
     */
    public function getDataFormatter() {
        return parent::getDataFormatter();
    }
}
