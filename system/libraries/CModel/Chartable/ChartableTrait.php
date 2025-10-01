<?php
/**
 * @method static CModel_Chartable_GroupCollection countForGroup($groupColumn)
 * @method static CModel_Chartable_TimeCollection  countByHours($startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  countByDays($startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  countByWeeks($startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  countByMonths($startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  countByYears($startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  countGroupBy($groupBy, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  valuesByHours($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  valuesByDays($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  valuesByWeeks($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  valuesByMonths($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  valuesByYears($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  valuesGroupBy($groupBy, $value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  sumByHours($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  sumByDays($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  sumByWeeks($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  sumByMonths($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  sumByYears($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  sumGroupBy($groupBy, $value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  avgByHours($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  avgByDays($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  avgByWeeks($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  avgByMonths($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  avgByYears($value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  avgGroupBy($groupBy, $value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 * @method static CModel_Chartable_TimeCollection  aggregateGroupBy($method, $groupBy, $value, $startDate = null, $stopDate = null, $dateColumn = 'created')
 */
trait CModel_Chartable_ChartableTrait {
    /**
     * Counts the values for model at the range and previous range.
     *
     * @param CModel_Query $builder
     * @param string       $groupColumn
     *
     * @return CModel_Chartable_GroupCollection
     */
    public function scopeCountForGroup(CModel_Query $builder, $groupColumn) {
        $group = $builder->select("${groupColumn} as label", CDatabase::raw('count(*) as value'))
            ->groupBy($groupColumn)
            ->orderBy('value', 'desc')
            ->get()
            ->map(function (CModel $model) {
                return $model->forceFill([
                    'label' => (string) $model->label,
                    'value' => (int) $model->value,
                ]);
            });

        return new CModel_Chartable_GroupCollection($group);
    }

    /**
     * @param CModel_Query $builder
     * @param string       $value
     * @param null|mixed   $startDate
     * @param null|mixed   $stopDate
     * @param string       $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    private function groupByYears(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        $startDate = empty($startDate)
            ? CCarbon::now()->subYear(10)->addYearsNoOverflow()->startOfYear()
            : CCarbon::parse($startDate);

        $stopDate = empty($stopDate)
            ? CCarbon::now()->endOfYear()
            : CCarbon::parse($stopDate);

        $newQuery = new CDatabase_Query_Builder($builder->getConnection());
        $newQuery->from($builder, 'chartable_sub');
        $query = $newQuery->select(
            CDatabase::raw("${value} as value"),
            CDatabase::raw("YEAR(${dateColumn}) as label")
        )
            ->where($dateColumn, '>=', $startDate)
            ->where($dateColumn, '<=', $stopDate)
            ->orderBy('label')
            ->groupBy('label')
            ->get();

        $years = $startDate->diffInYears($stopDate) + 1;

        return CModel_Chartable_TimeCollection::times($years, function () use ($startDate, $query) {
            $found = $query->firstWhere(
                'label',
                $startDate->format('Y')
            );

            $result = [
                'value' => ($found ? $found->value : 0),
                'label' => $startDate->format('Y'),
            ];

            $startDate->addYearsNoOverflow();

            return $result;
        });
    }

    /**
     * @param CModel_Query $builder
     * @param string       $value
     * @param null|mixed   $startDate
     * @param null|mixed   $stopDate
     * @param string       $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    private function groupByMonths(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        $startDate = empty($startDate)
            ? CCarbon::now()->subYear()->addMonthsNoOverflow()->startOfMonth()
            : CCarbon::parse($startDate);

        $stopDate = empty($stopDate)
            ? CCarbon::now()->endOfMonth()
            : CCarbon::parse($stopDate);

        $newQuery = new CDatabase_Query_Builder($builder->getConnection());
        $newQuery->from($builder, 'chartable_sub');
        $query = $newQuery->select(
            CDatabase::raw("${value} as value"),
            CDatabase::raw("CONCAT(YEAR(${dateColumn}),LPAD(MONTH(${dateColumn}),2,'0')) as label")
        )
            ->where($dateColumn, '>=', $startDate)
            ->where($dateColumn, '<=', $stopDate)
            ->orderBy('label')
            ->groupBy('label')
            ->get();

        $months = $startDate->diffInMonths($stopDate) + 1;

        return CModel_Chartable_TimeCollection::times($months, function () use ($startDate, $query) {
            $found = $query->firstWhere(
                'label',
                $startDate->format('Ym')
            );

            $result = [
                'value' => ($found ? $found->value : 0),
                'label' => $startDate->format('Ym'),
            ];

            $startDate->addMonthsNoOverflow();

            return $result;
        });
    }

    /**
     * @param CModel_Query $builder
     * @param string       $value
     * @param null|mixed   $startDate
     * @param null|mixed   $stopDate
     * @param string       $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    private function groupByWeeks(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        $startDate = empty($startDate)
            ? CCarbon::now()->subYear()->addMonthsNoOverflow()->startOfWeek()
            : CCarbon::parse($startDate);

        $stopDate = empty($stopDate)
            ? CCarbon::now()->endOfWeek()
            : CCarbon::parse($stopDate);
        $driver = $builder->getConnection()->getDriverName();
        // $dateExpression = 'CONCAT(YEAR(' . $dateColumn . '),LPAD(MONTH(' . $dateColumn . "),2,'0'),'-W',FLOOR((DayOfMonth(${dateColumn})-1)/7)+1)";
        // if ($driver == 'pgsql') {
        //     $dateExpression = "TO_CHAR(created_at, 'YYYY-MM-DD')";
        // }

        $newQuery = new CDatabase_Query_Builder($builder->getConnection());
        $newQuery->from($builder, 'chartable_sub');
        $query = $newQuery->select(
            CDatabase::raw("${value} as value"),
            CDatabase::raw("CONCAT(YEAR(${dateColumn}),LPAD(MONTH(${dateColumn}),2,'0'),'-W',FLOOR((DayOfMonth(${dateColumn})-1)/7)+1) as label")
        )
            ->where($dateColumn, '>=', $startDate)
            ->where($dateColumn, '<=', $stopDate)
            ->orderBy('label')
            ->groupBy('label')
            ->get();

        $weeks = $startDate->diffInWeeks($stopDate) + 1;

        $result = CModel_Chartable_TimeCollection::times($weeks, function () use ($startDate, $query) {
            $weekOfMonth = ceil($startDate->format('j') / 7);

            $found = $query->firstWhere(
                'label',
                $startDate->format('Ym') . '-W' . $weekOfMonth
            );

            $result = [
                'value' => ($found ? $found->value : 0),
                'label' => $startDate->format('Ym') . '-W' . $weekOfMonth,
            ];

            $startDate->addWeek();

            return $result;
        });

        return $result;
    }

    /**
     * @param CModel_Query $builder
     * @param string       $value
     * @param null|mixed   $startDate
     * @param null|mixed   $stopDate
     * @param string       $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    private function groupByDays(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        $startDate = empty($startDate)
            ? CCarbon::now()->subMonth()
            : CCarbon::parse($startDate);

        $stopDate = empty($stopDate)
            ? CCarbon::now()
            : CCarbon::parse($stopDate);
        $newQuery = new CDatabase_Query_Builder($builder->getConnection());
        $newQuery->from($builder, 'chartable_sub');
        $driver = $builder->getConnection()->getDriverName();
        $dateExpression = 'DATE_FORMAT(' . $dateColumn . ", '%Y-%m-%d')";
        if ($driver == 'pgsql') {
            $dateExpression = "TO_CHAR(created_at, 'YYYY-MM-DD')";
        }
        $query = $newQuery->select(
            CDatabase::raw("${value} as value"),
            CDatabase::raw($dateExpression . ' as label')
        )
            ->where($dateColumn, '>=', $startDate)
            ->where($dateColumn, '<=', $stopDate)
            ->orderBy('label')
            ->groupBy('label')
            ->get();

        $days = $startDate->diffInDays($stopDate) + 1;

        return CModel_Chartable_TimeCollection::times($days, function () use ($startDate, $query) {
            $found = $query->firstWhere(
                'label',
                $startDate->startOfDay()->toDateString()
            );

            $result = [
                'value' => ($found ? $found->value : 0),
                'label' => $startDate->toDateString(),
            ];

            $startDate->addDay();

            return $result;
        });
    }

    /**
     * @param CModel_Query $builder
     * @param string       $value
     * @param null|mixed   $startDate
     * @param null|mixed   $stopDate
     * @param string       $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    private function groupByHours(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        $startDate = empty($startDate)
            ? CCarbon::now()->subDay()
            : CCarbon::parse($startDate);

        $stopDate = empty($stopDate)
            ? CCarbon::now()->endOfDay()
            : CCarbon::parse($stopDate);

        $newQuery = new CDatabase_Query_Builder($builder->getConnection());
        $newQuery->from($builder, 'chartable_sub');

        $dateExpression = 'DATE_FORMAT(' . $dateColumn . ", '%Y-%m-%d %H')";
        $driver = $builder->getConnection()->getDriverName();
        if ($driver == 'pgsql') {
            $dateExpression = "TO_CHAR(created_at, 'YYYY-MM-DD HH24')";
        }
        $query = $newQuery->select(
            CDatabase::raw("${value} as value"),
            CDatabase::raw($dateExpression . ' as label')
        )
            ->where($dateColumn, '>=', $startDate)
            ->where($dateColumn, '<=', $stopDate)
            ->orderBy('label')
            ->groupBy('label');

        $collection = $query->get();
        $days = $startDate->diffInHours($stopDate) + 1;

        return CModel_Chartable_TimeCollection::times($days, function () use ($startDate, $collection) {
            $found = $collection->firstWhere(
                'label',
                $startDate->format('Y-m-d H')
            );

            $result = [
                'value' => ($found ? $found->value : 0),
                'label' => $startDate->format('Y-m-d H'),
            ];

            $startDate->addHour();

            return $result;
        });
    }

    /**
     * Get total models grouped by `created` hours.
     *
     * @param CModel_Query                  $builder
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeCountByHours(CModel_Query $builder, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByHours($builder, 'count(*)', $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get total models grouped by `created` days.
     *
     * @param CModel_Query                  $builder
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeCountByDays(CModel_Query $builder, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByDays($builder, 'count(*)', $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get total models grouped by `created` months.
     *
     * @param CModel_Query                  $builder
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeCountByMonths(CModel_Query $builder, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByMonths($builder, 'count(*)', $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get total models grouped by `created` weeks.
     *
     * @param CModel_Query                  $builder
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeCountByWeeks(CModel_Query $builder, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByWeeks($builder, 'count(*)', $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get total models grouped by `created` years.
     *
     * @param CModel_Query                  $builder
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeCountByYears(CModel_Query $builder, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByYears($builder, 'count(*)', $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get total models grouped by `created` years.
     *
     * @param CModel_Query                  $builder
     * @param string                        $groupBy
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeCountGroupBy(CModel_Query $builder, $groupBy, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->scopeAggregateGroupBy($builder, 'count', $groupBy, 'count(*)', $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get values models grouped by `created` hours.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeValuesByHours(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByHours($builder, $value, $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get values models grouped by `created` days.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeValuesByDays(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByDays($builder, $value, $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get values models grouped by `created` weeks.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeValuesByWeeks(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByWeeks($builder, $value, $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get values models grouped by `created` months.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeValuesByMonths(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByMonths($builder, $value, $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get values models grouped by `created` years.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeValuesByYears(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByYears($builder, $value, $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get values models grouped by `created` years.
     *
     * @param CModel_Query                  $builder
     * @param string                        $groupBy
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeValuesGroupBy(CModel_Query $builder, $groupBy, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->scopeAggregateGroupBy($builder, 'values', $groupBy, $value, $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get sum values models grouped by `created` hours.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeSumByHours(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByHours($builder, "SUM(${value})", $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get sum values models grouped by `created` days.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeSumByDays(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByDays($builder, "SUM(${value})", $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get sum values models grouped by `created` months.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeSumByMonths(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByMonths($builder, "SUM(${value})", $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get sum values models grouped by `created` weeks.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeSumByWeeks(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByWeeks($builder, "SUM(${value})", $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get sum values models grouped by `created` years.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeSumByYears(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByYears($builder, "SUM(${value})", $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get sum values models grouped by `created` years.
     *
     * @param CModel_Query                  $builder
     * @param string                        $groupBy
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeSumGroupBy(CModel_Query $builder, $groupBy, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->scopeAggregateGroupBy($builder, 'sum', $groupBy, $value, $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get sum values models grouped by `created` hours.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeAvgByHours(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByHours($builder, "AVG(${value})", $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get sum values models grouped by `created` days.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeAvgByDays(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByDays($builder, "AVG(${value})", $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get sum values models grouped by `created` months.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeAvgByMonths(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByMonths($builder, "AVG(${value})", $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get sum values models grouped by `created` weeks.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeAvgByWeeks(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByWeeks($builder, "AVG(${value})", $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get sum values models grouped by `created` years.
     *
     * @param CModel_Query                  $builder
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeAvgByYears(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->groupByYears($builder, "AVG(${value})", $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get sum values models grouped by `created` years.
     *
     * @param CModel_Query                  $builder
     * @param string                        $groupBy
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeAvgGroupBy(CModel_Query $builder, $groupBy, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        return $this->scopeAggregateGroupBy($builder, 'avg', $groupBy, $value, $startDate, $stopDate, $dateColumn);
    }

    /**
     * Get sum values models grouped by `created` years.
     *
     * @param CModel_Query                  $builder
     * @param string                        $groupBy
     * @param string                        $value
     * @param null|string|DateTimeInterface $startDate
     * @param null|string|DateTimeInterface $stopDate
     * @param string                        $dateColumn
     * @param mixed                         $method
     *
     * @return CModel_Chartable_TimeCollection
     */
    public function scopeAggregateGroupBy(CModel_Query $builder, $method, $groupBy, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        $val = $value;
        if ($method == 'sum') {
            $val = "SUM(${value})";
        }
        if ($method == 'avg') {
            $val = "AVG(${value})";
        }
        if ($groupBy == 'month') {
            return $this->groupByMonths($builder, $val, $startDate, $stopDate, $dateColumn);
        }
        if ($groupBy == 'week') {
            return $this->groupByWeeks($builder, $val, $startDate, $stopDate, $dateColumn);
        }
        if ($groupBy == 'day') {
            return $this->groupByDays($builder, $val, $startDate, $stopDate, $dateColumn);
        }
        if ($groupBy == 'hour') {
            return $this->groupByHours($builder, $val, $startDate, $stopDate, $dateColumn);
        }

        return $this->groupByYears($builder, $val, $startDate, $stopDate, $dateColumn);
    }
}
