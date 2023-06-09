<?php
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter\InListFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\BetweenFilter;
use Google\Analytics\Data\V1beta\Filter\NumericFilter;
use Google\Analytics\Data\V1beta\FilterExpressionList;

class CAnalytics_Google_AnalyticGA4_FilterBuilder {
    /**
     * @var CAnalytics_Google_AnalyticGA4_FilterBuilder[]
     */
    protected $andGroup = [];

    /**
     * @var CAnalytics_Google_AnalyticGA4_FilterBuilder[]
     */
    protected $orGroup = [];

    /**
     * @var CAnalytics_Google_AnalyticGA4_FilterBuilder
     */
    protected $notExpression = null;

    /**
     * @var CAnalytics_Google_AnalyticGA4_Filter
     */
    protected $filter = null;

    public function __construct() {
    }

    public function where($field, $callback = null) {
        if ($field instanceof Closure) {
        }

        $this->filter = new CAnalytics_Google_AnalyticGA4_Filter($field);
        if ($callback != null && $callback instanceof Closure) {
            $callback($this->filter);
        }

        return $this;
    }

    /**
     * @return FilterExpression
     */
    public function toGA4Object() {
        $data = [];
        if (count($this->andGroup) > 0) {
            $data['and_group'] = new FilterExpressionList(c::collect($this->andGroup)->map(function (CAnalytics_Google_AnalyticGA4_FilterBuilder $filter) {
                return $filter->toGA4Object();
            }));
        }
        if (count($this->orGroup) > 0) {
            $data['or_group'] = new FilterExpressionList(c::collect($this->andGroup)->map(function (CAnalytics_Google_AnalyticGA4_FilterBuilder $filter) {
                return $filter->toGA4Object();
            }));
        }

        if ($this->notExpression) {
            $data['or_group'] = $this->notExpression->toGA4Object();
        }

        $data['filter'] = $this->filter->toGA4Object();

        return new FilterExpression($data);
    }
}
