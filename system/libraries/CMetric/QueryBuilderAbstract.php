<?php
/**
 * Class CMetric_QueryBuilderAbstract.
 *
 * @see CDatabase_Query_Builder
 */
abstract class CMetric_QueryBuilderAbstract {
    /**
     * The bucket which the query is targeting.
     *
     * @var string
     */
    protected $from;

    /**
     * @var CPeriod
     */
    protected $period;

    /**
     * @param string $from
     *
     * @return $this
     */
    public function setFrom($from) {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string
     */
    public function getFrom() {
        return $this->from;
    }

    public function setPeriod(CPeriod $period) {
        $this->period = $period;

        return $this;
    }

    public function getPeriod() {
        return $this->period;
    }
}
