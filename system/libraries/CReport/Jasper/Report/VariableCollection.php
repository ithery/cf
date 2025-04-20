<?php

use Illuminate\Contracts\Support\Arrayable;

/**
 * @see CModel_Collection
 */
class CReport_Jasper_Report_VariableCollection extends CCollection {
    /**
     * Find a model in the collection by key.
     *
     * @param mixed $name
     * @param mixed $default
     *
     * @return CReport_Jasper_Report_Variable|static
     */
    public function find($name, $default = null) {
        if ($name instanceof CReport_Jasper_Report_Variable) {
            $name = $name->getName();
        }

        if ($name instanceof Arrayable) {
            $name = $name->toArray();
        }

        if (is_array($name)) {
            if ($this->isEmpty()) {
                return new static();
            }

            return $this->filter(function (CReport_Jasper_Report_Variable $variable) use ($name) {
                return $variable->getName() == $name;
            });
        }

        return carr::first($this->items, function (CReport_Jasper_Report_Variable $variable) use ($name) {
            return $variable->getName() == $name;
        }, $default);
    }
}
