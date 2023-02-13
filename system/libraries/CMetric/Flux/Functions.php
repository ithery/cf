<?php
class CMetric_Flux_Functions {
    protected $functions;

    public function first() {
        $this->functions .= PHP_EOL
            . '|> first() ';
    }

    public function last() {
        $this->functions .= PHP_EOL
            . '|> last() ';
    }

    /**
     * @param string           $column
     * @param string|int|float $value
     *
     * @return CMetric_Flux_Functions
     */
    public function fill($value, $column = null) {
        $columnParam = '';
        if ($column) {
            $columnParam = 'column: "' . $column . '", ';
        }
        $val = $value;
        if (is_string($value)) {
            $val = '"' . $value . '"';
        }
        $this->functions .= PHP_EOL
            . '|> fill(' . $columnParam . 'value:' . $val . ') ';

        return $this;
    }

    /**
     * @param string $column
     *
     * @return CMetric_Flux_Functions
     */
    public function fillPrevious($column = null) {
        $columnParam = '';
        if ($column) {
            $columnParam = 'column: "' . $column . '", ';
        }
        $this->functions .= PHP_EOL
            . '|> fill(' . $columnParam . 'usePrevious: true) ';

        return $this;
    }

    /**
     * @param string $column
     *
     * @return CMetric_Flux_Functions
     */
    public function unique($column) {
        $this->functions .= PHP_EOL
            . '|> unique(column: "' . $column . '") ';

        return $this;
    }

    public function group(array $columns) {
        $this->functions .= PHP_EOL
        . '|> group(columns: [' . implode(' ,', c::collect($columns)->map(function ($s) {
            return '"' . $s . '"';
        })->toArray()) . ']) ';

        return $this;
    }

    public function keepColumns(array $columns) {
        $this->functions .= PHP_EOL
        . '|> keep(columns: ["' . implode(' ,', c::collect($columns)->map(function ($s) {
            return '"' . $s . '"';
        })->toArray()) . '"]) ';

        return $this;
    }

    public function dropColumns(array $columns) {
        $this->functions .= PHP_EOL
        . '|> drop(columns: ["' . implode(' ,', c::collect($columns)->map(function ($s) {
            return '"' . $s . '"';
        })->toArray()) . '"]) ';

        return $this;
    }

    public function top($n, array $columns = []) {
        return $this->topOrbottom($n, $columns, 'top');
    }

    public function bottom($n, array $columns = []) {
        return $this->topOrbottom($n, $columns, 'bottom');
    }

    private function topOrbottom($n, array $columns = [], $method = 'top') {
        if ($columns != null && count($columns) > 0) {
            $this->functions .= PHP_EOL
            . '|> ' . $method . '(n:' . $n . ', columns: ["' . implode(' ,', c::collect($columns)->map(function ($s) {
                return '"' . $s . '"';
            })->toArray()) . '"]) ';
        } else {
            $this->functions .= PHP_EOL
            . '|> ' . $method . '(n:' . $n . ') ';
        }

        return $this;
    }

    public function stateDuration($column, $value) {
        $val = $value;
        if (is_string($value)) {
            $val = '"' . $value . '"';
        }
        $this->functions .= PHP_EOL
        . '|> stateDuration(fn: (r) => r.' . $column . ' == ' . $val . ', column:"' . $column . '") ';
    }

    public function stateCount($column, $value) {
        $val = $value;
        if (is_string($value)) {
            $val = '"' . $value . '"';
        }
        $this->functions .= PHP_EOL
        . '|> stateCount(fn: (r) => r.' . $column . ' == ' . $val . ', column:"' . $column . '") ';
    }

    public function pivot(array $tags = []) {
        $columns = '';
        if ($tags != null && count($tags) > 0) {
            $columns = implode(' ,', c::collect($tags)->map(function ($s) {
                return '"' . $s . '"';
            })->toArray());
            $columns = '"time_",' . $columns;
        } else {
            $columns = '"time_"';
        }
        $this->functions .= PHP_EOL
        . '|> pivot(rowKey:[' . $columns . '], columnKey:["_field"]. valueColumn:"_value") ';

        return $this;
    }

    public function getFunctions() {
        return $this->functions;
    }
}
