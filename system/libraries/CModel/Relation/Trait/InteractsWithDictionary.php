<?php
trait CModel_Relation_Trait_InteractsWithDictionary {
    /**
     * Get a dictionary key attribute - casting it to a string if necessary.
     *
     * @param mixed $attribute
     *
     * @throws \Doctrine\Instantiator\Exception\InvalidArgumentException
     *
     * @return mixed
     */
    protected function getDictionaryKey($attribute) {
        if (is_object($attribute)) {
            if (method_exists($attribute, '__toString')) {
                return $attribute->__toString();
            }

            throw new InvalidArgumentException('Model attribute value is an object but does not have a __toString method.');
        }

        return $attribute;
    }
}
