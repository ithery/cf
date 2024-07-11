<?php

class CReport_Builder_Dictionary {
    /**
     * @var CReport_Builder_Dictionary_VariableCollection
     */
    protected $variables;

    /**
     * @var CCollection
     */
    protected $parameters;

    public function __construct() {
        $this->variables = new CReport_Builder_Dictionary_VariableCollection();
        $this->parameters = new CCollection();
    }

    public function fillVariables(CCollection $variableElements) {
        foreach ($variableElements as $variable) {
            $this->variables->push(new CReport_Builder_Dictionary_Variable($variable));
        }
    }

    /**
     * @return CCollection
     */
    public function getParameters() {
        return $this->parameters;
    }

    public function resetVariableForGroup($groupName) {
        $variables = $this->variables->filter(function (CReport_Builder_Dictionary_Variable $variable) use ($groupName) {
            return $variable->getResetType() == CReport::RESET_TYPE_GROUP && $variable->getResetGroup() == $groupName;
        });
        foreach ($variables as $variable) {
            /** @var CReport_Builder_Dictionary_Variable $variable */
            $variable->unsetValue();
        }
    }

    /**
     * @return CReport_Builder_Dictionary_VariableCollection
     */
    public function getVariables() {
        return $this->variables;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParameterValue($key, $default = null) {
        return carr::get($this->parameters, $key, $default);
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getVariableValue($name, $default = null) {
        $variable = $this->variables->find($name);

        if ($variable) {
            return $variable->getValue();
        }

        return $default;
    }
}
