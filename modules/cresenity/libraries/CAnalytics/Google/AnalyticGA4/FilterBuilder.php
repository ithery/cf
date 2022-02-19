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

    protected $fieldName = null;

    public function __construct() {
    }

    public function toGa4Filter() {
        $data = [];
        if (count($this->andGroup) > 0) {
            $data['and_group'] = new FilterExpressionList(c::collect($this->andGroup)->map(function (CAnalytics_Google_AnalyticGA4_FilterBuilder $filter) {
                return $filter->toGa4Filter();
            }));
        }
        if (count($this->orGroup) > 0) {
            $data['or_group'] = new FilterExpressionList(c::collect($this->andGroup)->map(function (CAnalytics_Google_AnalyticGA4_FilterBuilder $filter) {
                return $filter->toGa4Filter();
            }));
        }

        if ($this->notExpression) {
            $data['or_group'] = $this->notExpression->toGa4Filter();
        }

        $data['filter'] = new Filter();

        return new FilterExpression($data);
    }
}
