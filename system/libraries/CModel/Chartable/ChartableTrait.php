<?php
/**
 * @method static CModel_Chartable_GroupCollection countForGroup()
 * @method static CModel_Chartable_TimeCollection  countByDays()
 * @method static CModel_Chartable_TimeCollection  countByMonths()
 * @method static CModel_Chartable_TimeCollection  valuesByDays()
 * @method static CModel_Chartable_TimeCollection  sumByDays()
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
    private function groupByMonths(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        $startDate = empty($startDate)
            ? CCarbon::now()->subYear()->addMonthsNoOverflow()->startOfMonth()
            : CCarbon::parse($startDate);

        $stopDate = empty($stopDate)
            ? CCarbon::now()->endOfMonth()
            : CCarbon::parse($stopDate);

        $query = $builder->select(
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
                $startDate->format('Ym'),
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
    private function groupByDays(CModel_Query $builder, $value, $startDate = null, $stopDate = null, $dateColumn = 'created') {
        $startDate = empty($startDate)
            ? CCarbon::now()->subMonth()
            : CCarbon::parse($startDate);

        $stopDate = empty($stopDate)
            ? CCarbon::now()
            : CCarbon::parse($stopDate);

        $query = $builder->select(
            CDatabase::raw("${value} as value"),
            CDatabase::raw("DATE(${dateColumn}) as label")
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
     * Get total models grouped by `created_at` day.
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
     * Get total models grouped by `created_at` day.
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
     * Get values models grouped by `created_at` day.
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
     * Get sum values models grouped by `created_at` day.
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
}
