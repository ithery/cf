<?php

use Illuminate\Contracts\Support\Arrayable;

/**
 * Description of Textarea.
 *
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jan 28, 2018, 9:50:24 PM
 */
class CElement_FormInput_QueryBuilder extends CElement_FormInput {
    protected $filters;

    protected $inputId;

    protected $input;

    protected $container;

    protected $containerId;

    public function __construct($id) {
        if ($id == '') {
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

        return "
        $('#" . $this->containerId . "').queryBuilder({

            filters: " . c::json($filters) . ',

            rules: ' . c::json($this->value) . ',
        });
        let error_' . $this->inputId . "= null;
        $('#" . $this->containerId . "').on('validationError.queryBuilder', function(e, rule, error, value) {
            error_" . $this->inputId . "= error;

        });
        let form = $('#" . $this->inputId . "').closest('form');
        if(form.length>0) {
            form.on('submit',()=>{


                const result = $('#" . $this->containerId . "').queryBuilder('getRules');
                console.log('form submit', error_" . $this->inputId . ');
                if(error_' . $this->inputId . ") {
                    cresenity.toast('error', Array.isArray(error_" . $this->inputId . '  )?error_' . $this->inputId . '[0]:error_' . $this->inputId . ');
                    error_' . $this->inputId . " = null;
                    return false;
                }
                $('#" . $this->inputId . "').val(JSON.stringify(result, null, 2));
                return true;
            });
        }


        ";
    }
}
