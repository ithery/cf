<?php
trait CModel_Factory_HasFactoryTrait {
    /**
     * Get a new factory instance for the model.
     *
     * @param null|callable|array|int $count
     * @param callable|array          $state
     *
     * @return \CModel_Factory_Factory<static>
     */
    public static function factory($count = null, $state = []) {
        $factory = static::newFactory() ?: CModel_Factory_Factory::factoryForModel(get_called_class());

        return $factory
            ->count(is_numeric($count) ? $count : null)
            ->state(is_callable($count) || is_array($count) ? $count : $state);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \CModel_Factory_Factory<static>
     */
    protected static function newFactory() {
    }
}
