<?php

class CBase_Benchmark {
    /**
     * Measure a callable or array of callables over the given number of iterations.
     *
     * @param \Closure|array $benchmarkables
     * @param int            $iterations
     *
     * @return array|float
     */
    public static function measure($benchmarkables, $iterations = 1) {
        return c::collect(carr::wrap($benchmarkables))->map(function ($callback) use ($iterations) {
            return c::collect(range(1, $iterations))->map(function () use ($callback) {
                gc_collect_cycles();

                $start = hrtime(true);

                $callback();

                return (hrtime(true) - $start) / 1000000;
            })->average();
        })->when(
            $benchmarkables instanceof Closure,
            function ($c) {
                return $c->first();
            },
            function ($c) {
                return $c->all();
            }
        );
    }

    /**
     * Measure a callable or array of callables over the given number of iterations, then dump and die.
     *
     * @param \Closure|array $benchmarkables
     * @param int            $iterations
     *
     * @return void
     */
    public static function dd($benchmarkables, $iterations = 1) {
        $result = c::collect(static::measure(carr::wrap($benchmarkables), $iterations))
            ->map(fn ($average) => number_format($average, 3) . 'ms')
            ->when(
                $benchmarkables instanceof Closure,
                function ($c) {
                    return $c->first();
                },
                function ($c) {
                    return $c->all();
                }
            );

        cdbg::dd($result);
    }
}
