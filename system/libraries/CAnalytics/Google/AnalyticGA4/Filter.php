<?php
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\InListFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\BetweenFilter;
use Google\Analytics\Data\V1beta\Filter\NumericFilter;

class CAnalytics_Google_AnalyticGA4_Filter {
    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var CAnalytics_Google_AnalyticGA4_OperatorAbstract
     */
    protected $operator;

    public function __construct($fieldName) {
        $this->fieldName = $fieldName;
    }

    /**
     * @return CAnalytics_Google_AnalyticGA4_Operator_StringOperator
     */
    public function string() {
        $this->operator = new CAnalytics_Google_AnalyticGA4_Operator_StringOperator();

        return $this->operator;
    }

    public function toGA4Object() {
        $data = [];
        $data['field_name'] = $this->fieldName;
        $operatorGA4Object = $this->operator->toGA4Object();
        if ($operatorGA4Object instanceof StringFilter) {
            $data['string_filter'] = $operatorGA4Object;
        }
        if ($operatorGA4Object instanceof InListFilter) {
            $data['in_list_filter'] = $operatorGA4Object;
        }
        if ($operatorGA4Object instanceof NumericFilter) {
            $data['numeric_filter'] = $operatorGA4Object;
        }
        if ($operatorGA4Object instanceof BetweenFilter) {
            $data['between_filter'] = $operatorGA4Object;
        }

        return new Filter($data);
    }
}
