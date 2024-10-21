<?php

/**
 * @template TKey of array-key
 * @template TItem
 *
 * @extends  \ArrayObject<TKey, TItem>
 */
class CModel_Casts_ArrayObject extends ArrayObject implements CInterface_Arrayable, JsonSerializable {
    /**
     * Get a collection containing the underlying array.
     *
     * @return \CCollection
     */
    public function collect() {
        return c::collect($this->getArrayCopy());
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() {
        return $this->getArrayCopy();
    }

    /**
     * Get the array that should be JSON serialized.
     *
     * @return array
     */
    public function jsonSerialize(): array {
        return $this->getArrayCopy();
    }
}
