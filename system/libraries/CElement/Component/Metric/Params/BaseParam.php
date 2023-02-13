<?php

class CElement_Component_Metric_Params_BaseParam {
    /**
     * The displayable name of the metric.
     *
     * @var string
     */
    protected $name;

    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Calculate the metric's value.
     *
     * @param mixed $request
     *
     * @return mixed
     */
    public function resolve($request) {
        $resolver = function () use ($request) {
            return $this->onlyOnDetail
                    ? $this->calculate($request, $request->findModelOrFail())
                    : $this->calculate($request);
        };

        if ($cacheFor = $this->cacheFor()) {
            $cacheFor = is_numeric($cacheFor) ? new DateInterval(sprintf('PT%dS', $cacheFor * 60)) : $cacheFor;

            return c::cache()->remember(
                $this->getCacheKey($request),
                $cacheFor,
                $resolver
            );
        }

        return $resolver();
    }

    /**
     * Get the appropriate cache key for the metric.
     *
     * @param array $request
     *
     * @return string
     */
    protected function getCacheKey($request) {
        return sprintf(
            'capp.metric.%s.%s.%s.%s.%s',
            $this->uriKey(),
            carr::get($request, 'range', 'no-range'),
            carr::get($request, 'timezone', 'no-timezone'),
            carr::get($request, 'twelveHourTime', 'no-12-hour-time'),
            $this->onlyOnDetail ? carr::get($request, 'modelKey') : 'no-resource-id'
        );
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey() {
        return cstr::slug($this->getName(), '-', null);
    }

    public function getName() {
        return $this->name;
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor() {
    }

    /**
     * Convert datetime to application timezone.
     *
     * @param \CCarbon $datetime
     *
     * @return \CCarbon
     */
    protected function asQueryDatetime($datetime) {
        if (!$datetime instanceof \DateTimeImmutable) {
            return $datetime->copy()->timezone(CF::config('app.timezone'));
        }

        return $datetime->timezone(CF::config('app.timezone'));
    }
}
