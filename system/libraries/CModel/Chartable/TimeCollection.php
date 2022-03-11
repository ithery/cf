<?php

class CModel_Chartable_TimeCollection extends CCollection {
    /**
     * @param string        $name
     * @param null|\Closure $closure
     *
     * @return array
     */
    public function toChart($name = 'Chart', Closure $closure = null) {
        $closure = $closure ?: static function ($label) {
            return $label;
        };

        return [
            'name' => $name,
            'labels' => $this->pluck('label')->map($closure)->toArray(),
            'values' => $this->pluck('value')->toArray(),
        ];
    }

    /**
     * @return CModel_Chartable_TimeCollection
     */
    public function showDaysOfWeek() {
        return $this->transformLabel(function (array $value) {
            $day = CCarbon::parse($value['label'])->dayName;

            return cstr::ucfirst($day);
        });
    }

    /**
     * @return CModel_Chartable_TimeCollection
     */
    public function showMinDaysOfWeek() {
        return $this->transformLabel(function (array $value) {
            $day = CCarbon::parse($value['label'])->minDayName;

            return cstr::ucfirst($day);
        });
    }

    /**
     * @param callable $callback
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function transformLabel($callback) {
        return $this->transform(function (array $value) use ($callback) {
            $value['label'] = $callback($value);

            return $value;
        });
    }
}
