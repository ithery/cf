<?php

class CSerializer_DeepCopySerializer extends CSerializer_Serializer {
    /**
     * Extract the data from an object.
     *
     * @param mixed $value
     *
     * @return array
     */
    protected function serializeObject($value) {
        if (self::$objectStorage->contains($value)) {
            return self::$objectStorage[$value];
        }

        $reflection = new ReflectionClass($value);
        $className = $reflection->getName();

        $serialized = $this->serializeInternalClass($value, $className, $reflection);
        self::$objectStorage->attach($value, $serialized);

        return $serialized;
    }
}
