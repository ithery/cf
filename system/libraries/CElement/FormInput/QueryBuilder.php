<?php

use Illuminate\Contracts\Support\Arrayable;

class CElement_FormInput_QueryBuilder extends CElement_FormInput {
    /**
     * @var CElement_FormInput_QueryBuilder_FilterBuilder
     */
    protected $filters;

    protected $inputId;

    protected $input;

    protected $container;

    protected $containerId;

    protected $isApplySelect2;

    public function __construct($id = null) {
        if ($id == null) {
            $id = spl_object_hash($this);
        }
        $this->id = $id;
        $this->inputId = $id;
        $this->id = $this->id . '-wrapper';
        parent::__construct($this->id);
        $this->containerId = $this->id . '-container';

        $this->tag = 'div';
        $this->isOneTag = false;
        $this->container = CElement_Element_Div::factory($this->containerId);
        $this->input = CElement_FormInput_Hidden::factory($this->inputId);
        $this->add($this->container);
        $this->add($this->input);
        $this->addClass('capp-query-builder capp-input');
        $this->value = [];
    }

    /**
     * Parse the given rules and return a CModel_Query object from the given model class.
     *
     * @param string $rules
     * @param string $modelClass
     *
     * @return CModel_Query
     */
    public static function parseToModelQuery($rules, $modelClass) {
        $parser = new CElement_FormInput_QueryBuilder_Parser($modelClass);

        return $parser->parse($rules);
    }

    public function withFilterBuilder($callback) {
        $this->filters = c::tap(new CElement_FormInput_QueryBuilder_FilterBuilder(), $callback);

        return $this;
    }

    /**
     * @return CElement_FormInput_QueryBuilder_FilterBuilder
     */
    public function filterBuilder() {
        if ($this->filters == null) {
            $this->filters = new CElement_FormInput_QueryBuilder_FilterBuilder();
        }

        return $this->filters;
    }

    public function setName($val) {
        $this->input->setName($val);

        return $this;
    }

    public function setApplySelect2($bool = true) {
        $this->isApplySelect2 = $bool;

        return $this;
    }

    public function build() {
        parent::build();
        if ($this->readonly) {
            $this->setAttr('readonly', 'readonly');
        }
        if ($this->disabled) {
            $this->setAttr('disabled', 'disabled');
        }
    }

    public function js($indent = 0) {
        $filters = $this->filters;
        if ($filters instanceof Arrayable) {
            $filters = $filters->toArray();
        }
        $js = parent::js($indent);
        $js .= '
        let qb_' . $this->inputId . " = $('#" . $this->containerId . "').queryBuilder({
            plugins: ['bt-tooltip-errors'],
            filters: " . c::json($filters) . ',
            allow_empty: true,
            rules: ' . c::json($this->value) . ',
        });
        let error_' . $this->inputId . " = null;
        $('#" . $this->containerId . "').on('validationError.queryBuilder', function(e, rule, error, value) {
            window.error_query_builder_" . $this->inputId . " = error;
            cresenity.toast('error', Array.isArray(window.error_query_builder_" . $this->inputId . '  ) ? window.error_query_builder_' . $this->inputId . '[0] : window.error_query_builder_' . $this->inputId . ");
        });
        let form = $('#" . $this->inputId . "').closest('form');
        if(form.length>0) {
            form.on('submit',()=>{
                const result = $('#" . $this->containerId . "').queryBuilder('getRules');
                console.log('form submit', error_" . $this->inputId . ');
                if(window.error_query_builder_' . $this->inputId . ") {
                    cresenity.toast('error', Array.isArray(window.error_query_builder_" . $this->inputId . '  ) ? window.error_query_builder_' . $this->inputId . '[0] : window.error_query_builder_' . $this->inputId . ');
                    window.error_query_builder_' . $this->inputId . " = null;
                    return false;
                }
                $('#" . $this->inputId . "').val(JSON.stringify(result, null, 2));
                return true;
            });
        }";

        if ($this->isApplySelect2) {
            $js .= '

        if ($.fn.select2) {
            qb_' . $this->inputId . ".on('afterCreateRuleInput.queryBuilder', function(e, rule) {
                if(rule.__ && rule.\$el) {
                    if(rule.__.filter) {
                        if(rule.__.filter.input && rule.__.filter.input=='select') {
                            $(rule.\$el).find('.rule-value-container select').css('min-width','200px');
                            $(rule.\$el).find('.rule-value-container select').select2();
                        }
                    }
                }

            });
        }

        ";
        }

        return $js;
    }
}
