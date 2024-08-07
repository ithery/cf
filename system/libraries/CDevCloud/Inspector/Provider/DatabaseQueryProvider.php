<?php

class CDevCloud_Inspector_Provider_DatabaseQueryProvider extends CDevCloud_Inspector_ProviderAbstract {
    public function boot() {
        try {
            CEvent::dispatcher()->listen(CDatabase_Event_QueryExecuted::class, function (CDatabase_Event_QueryExecuted $query) {
                $bindings = $query->bindings;
                $time = $query->time;
                $connection = $query->connection;
                $sql = $query->sql;
                if (CDevCloud::inspector()->canAddSegments()) {
                    $this->handleQueryReport($sql, $bindings, $time, $connection->getName());
                }
            });
        } catch (\Exception $e) {
            //do nothing
        }
    }

    /**
     * Attach a span to monitor query execution.
     *
     * @param string $sql
     * @param array  $bindings
     * @param $time
     * @param string $connection
     */
    protected function handleQueryReport($sql, array $bindings, $time, $connection) {
        $segment = CDevCloud::inspector()->startSegment($connection, substr($sql, 0, 50))
            ->start(microtime(true) - $time / 1000);

        $context = [
            'connection' => $connection,
            'query' => $sql,
        ];

        if (CF::config('devcloud.inspector.bindings')) {
            $context['bindings'] = $bindings;
        }

        $segment->addContext('db', $context)->end($time);
    }
}
