<?php
/**
 * @see CModel_Collection
 */
class CReport_Jasper_Report_ParameterCollection extends CCollection {
    /**
     * Find a model in the collection by key.
     *
     * @param mixed $name
     * @param mixed $default
     *
     * @return CReport_Jasper_Report_Parameter|static
     */
    public function find($name, $default = null) {
        if ($name instanceof CReport_Jasper_Report_Parameter) {
            $name = $name->getName();
        }

        if ($name instanceof CInterface_Arrayable) {
            $name = $name->toArray();
        }

        if (is_array($name)) {
            if ($this->isEmpty()) {
                return new static();
            }

            return $this->filter(function (CReport_Jasper_Report_Parameter $parameter) use ($name) {
                return $parameter->getName() == $name;
            });
        }

        return carr::first($this->items, function (CReport_Jasper_Report_Parameter $parameter) use ($name) {
            return $parameter->getName() == $name;
        }, $default);
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getValue(string $name, $default = null) {
        $parameter = $this->find($name);
        if ($parameter) {
            return $parameter->getValue();
        }

        return null;
    }

    public function getList() {
        return $this->mapWithKeys(function (CReport_Jasper_Report_Parameter $parameter) {
            return [$parameter->getName() => $parameter->getValue()];
        })->toArray();
    }
}
