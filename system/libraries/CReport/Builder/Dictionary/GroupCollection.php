<?php
/**
 * @see CModel_Collection
 */
class CReport_Builder_Dictionary_GroupCollection extends CCollection {
    /**
     * Find a model in the collection by key.
     *
     * @param mixed $name
     * @param mixed $default
     *
     * @return CReport_Builder_Dictionary_Group|static
     */
    public function find($name, $default = null) {
        if ($name instanceof CReport_Builder_Dictionary_Group) {
            $name = $name->getName();
        }

        if ($name instanceof CInterface_Arrayable) {
            $name = $name->toArray();
        }

        if (is_array($name)) {
            if ($this->isEmpty()) {
                return new static();
            }

            return $this->filter(function (CReport_Builder_Dictionary_Group $group) use ($name) {
                return $group->getName() == $name;
            });
        }

        return carr::first($this->items, function (CReport_Builder_Dictionary_Group $group) use ($name) {
            return $group->getName() == $name;
        }, $default);
    }
}
