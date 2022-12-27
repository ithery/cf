<?php

class CElement_Component_Metric_Params_ValueParam extends CElement_Component_Metric_Params_BaseParam {
    /**
     * Return a value result showing the growth of a model over a given time frame.
     *
     * @param \CHTTP_Request                          $request
     * @param \CModel_Query|string                    $model
     * @param string                                  $function
     * @param null|\CDatabase_Query_Expression|string $column
     * @param null|string                             $dateColumn
     *
     * @return \CElement_Component_Metric_Results_ValueResult
     */
    protected function aggregate($request, $model, $function, $column = null, $dateColumn = null) {
        $query = $model instanceof CModel_Query ? $model : (new $model())->newQuery();

        $column = $column ?? $query->getModel()->getQualifiedKeyName();

        if ($request->range === 'ALL') {
            return $this->result(
                round(c::with(clone $query)->{$function}($column), $this->precision)
            );
        }

        $timezone = c::resolveUserTimezone($request) ?? $request->timezone;

        $previousValue = round(c::with(clone $query)->whereBetween(
            $dateColumn ?? $query->getModel()->getQualifiedCreatedAtColumn(),
            array_map(function ($datetime) {
                return $this->asQueryDatetime($datetime);
            }, $this->previousRange($request->range, $timezone))
        )->{$function}($column), $this->precision);

        return $this->result(
            round(c::with(clone $query)->whereBetween(
                $dateColumn ?? $query->getModel()->getQualifiedCreatedAtColumn(),
                array_map(function ($datetime) {
                    return $this->asQueryDatetime($datetime);
                }, $this->currentRange($request->range, $timezone))
            )->{$function}($column), $this->precision)
        )->previous($previousValue);
    }

    /**
     * Calculate the previous range and calculate any short-cuts.
     *
     * @param string|int $range
     * @param string     $timezone
     *
     * @return array
     */
    protected function previousRange($range, $timezone) {
        if ($range == 'TODAY') {
            return [
                c::now($timezone)->modify('yesterday')->setTime(0, 0),
                c::today($timezone)->subSecond(1),
            ];
        }

        if ($range == 'MTD') {
            return [
                c::now($timezone)->modify('first day of previous month')->setTime(0, 0),
                c::now($timezone)->firstOfMonth()->subSecond(1),
            ];
        }

        if ($range == 'QTD') {
            return $this->previousQuarterRange($timezone);
        }

        if ($range == 'YTD') {
            return [
                c::now($timezone)->subYears(1)->firstOfYear()->setTime(0, 0),
                c::now($timezone)->firstOfYear()->subSecond(1),
            ];
        }

        return [
            c::now($timezone)->subDays($range * 2),
            c::now($timezone)->subDays($range)->subSecond(1),
        ];
    }

    /**
     * Calculate the previous quarter range.
     *
     * @param string $timezone
     *
     * @return array
     */
    protected function previousQuarterRange($timezone) {
        return [
            CCarbon::firstDayOfPreviousQuarter($timezone),
            CCarbon::firstDayOfQuarter($timezone)->subSecond(1),
        ];
    }

    /**
     * Calculate the current range and calculate any short-cuts.
     *
     * @param string|int $range
     * @param string     $timezone
     *
     * @return array
     */
    protected function currentRange($range, $timezone) {
        if ($range == 'TODAY') {
            return [
                c::today($timezone),
                c::now($timezone),
            ];
        }

        if ($range == 'MTD') {
            return [
                c::now($timezone)->firstOfMonth(),
                c::now($timezone),
            ];
        }

        if ($range == 'QTD') {
            return $this->currentQuarterRange($timezone);
        }

        if ($range == 'YTD') {
            return [
                c::now($timezone)->firstOfYear(),
                c::now($timezone),
            ];
        }

        return [
            c::now($timezone)->subDays($range),
            c::now($timezone),
        ];
    }

    /**
     * Calculate the previous quarter range.
     *
     * @param string $timezone
     *
     * @return array
     */
    protected function currentQuarterRange($timezone) {
        return [
            CCarbon::firstDayOfQuarter($timezone),
            c::now($timezone),
        ];
    }

    /**
     * Create a new value metric result.
     *
     * @param mixed $value
     *
     * @return \CElement_Component_Metric_Results_ValueResult
     */
    public function result($value) {
        return new CElement_Component_Metric_Results_ValueResult($value);
    }
}
