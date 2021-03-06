<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 2, 2019, 2:14:21 AM
 */
trait CQueue_Trait_SerializesModels {
    use CQueue_Trait_SerializesAndRestoresModelIdentifiers;

    /**
     * Prepare the instance for serialization.
     *
     * @return array
     */
    public function __sleep() {
        $properties = (new ReflectionClass($this))->getProperties();

        foreach ($properties as $property) {
            $property->setValue($this, $this->getSerializedPropertyValue(
                $this->getPropertyValue($property)
            ));
        }

        return array_values(array_filter(array_map(function ($p) {
            return $p->isStatic() ? null : $p->getName();
        }, $properties)));
    }

    /**
     * Restore the model after serialization.
     *
     * @return void
     */
    public function __wakeup() {
        foreach ((new ReflectionClass($this))->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }
            $property->setValue($this, $this->getRestoredPropertyValue(
                $this->getPropertyValue($property)
            ));
        }
    }

    /**
     * Get the property value for the given property.
     *
     * @param \ReflectionProperty $property
     *
     * @return mixed
     */
    protected function getPropertyValue(ReflectionProperty $property) {
        $property->setAccessible(true);

        return $property->getValue($this);
    }
}
