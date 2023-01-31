<?php
/**
 * Class CMetric_Metric.
 *
 * @see CMetric
 */
class CMetric_QueryBuilder extends CMetric_QueryBuilderAbstract {
    use CMetric_Trait_HasDriverTrait;
    protected $fluxQuery;

    public function __construct($driver = null) {
        $this->driver = $driver;
        $this->period = CPeriod::hours(4);
    }

    /**
     * @param Closure $callback
     *
     * @return CMetric_QueryBuilder
     */
    public function withFluxQuery(Closure $callback) {
        $this->fluxQuery = new CMetric_Flux_FluxQuery();
        $callback($this->fluxQuery);

        return $this;
    }

    /**
     * Undocumented function.
     *
     * @return null|CMetric_Flux_FluxQuery
     */
    public function getFluxQuery() {
        return $this->fluxQuery;
    }

    /**
     * @return CMetric_QueryResult
     */
    public function get() {
        return $this->getDriver()->query($this);
    }
}
