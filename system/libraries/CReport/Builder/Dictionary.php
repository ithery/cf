<?php

class CReport_Builder_Dictionary {
    protected $groups;

    protected $variables;

    protected $parameters;

    public function __construct() {
        $this->variables = new CReport_Builder_Dictionary_VariableCollection();
        $this->groups = new CReport_Builder_Dictionary_GroupCollection();
        $this->parameters = new CCollection();
    }

    /**
     * @return CCollection
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @return CReport_Builder_Dictionary_GroupCollection
     */
    public function getGroups() {
        return $this->groups;
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
