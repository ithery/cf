<?php

class CModel_Chartable_GroupCollection extends CCollection {
    /**
     * @param null|\Closure $closure
     *
     * @return array
     */
    public function toChart($closure = null) {
        $closure = $closure ?: static function ($label) {
            return $label;
        };

        return $this
            ->sortByDesc('value')
            ->pluck('label')
            ->map(function ($name) use ($closure) {
                return [
                    'labels' => $this->pluck('label')->map($closure)->toArray(),
                    'values' => $this->getChartsValues($name),
                ];
            })
            ->toArray();
    }

    /**
     * @param string $name
     *
     * @return array
     */
    private function getChartsValues($name) {
        return $this
            ->map(static function ($item) use ($name) {
                return $item->label === $name
                    ? (int) $item->value
                    : 0;
            })
            ->toArray();
    }
}
