<?php
interface CAuth_Contract_AuthorizableInterface {
    /**
     * Determine if the entity has a given ability.
     *
     * @param iterable|string $abilities
     * @param array|mixed     $arguments
     *
     * @return bool
     */
    public function can($abilities, $arguments = []);
}
