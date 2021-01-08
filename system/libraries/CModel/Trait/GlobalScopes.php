<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @see CModel
 * @since Dec 25, 2017, 10:08:50 PM
 */

trait CModel_Trait_GlobalScopes {
    /**
     * Register a new global scope on the model.
     *
     * @param CModel_Interface_Scope|\Closure|string $scope
     * @param \Closure|null                          $implementation
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public static function addGlobalScope($scope, Closure $implementation = null) {
        /** @var CModel $this */
        if (is_string($scope) && !is_null($implementation)) {
            return static::$globalScopes[static::class][$scope] = $implementation;
        } elseif ($scope instanceof Closure) {
            return static::$globalScopes[static::class][spl_object_hash($scope)] = $scope;
        } elseif ($scope instanceof CModel_Interface_Scope) {
            return static::$globalScopes[static::class][get_class($scope)] = $scope;
        }

        throw new InvalidArgumentException('Global scope must be an instance of Closure or Scope.');
    }

    /**
     * Determine if a model has a global scope.
     *
     * @param CModel_Interface_Scope|string $scope
     *
     * @return bool
     */
    public static function hasGlobalScope($scope) {
        return !is_null(static::getGlobalScope($scope));
    }

    /**
     * Get a global scope registered with the model.
     *
     * @param CModel_Interface_Scope|string $scope
     *
     * @return CModel_Interface_Scope|\Closure|null
     */
    public static function getGlobalScope($scope) {
        /** @var CModel $this */
        if (is_string($scope)) {
            return carr::get(static::$globalScopes, static::class . '.' . $scope);
        }

        return carr::get(
            static::$globalScopes,
            static::class . '.' . get_class($scope)
        );
    }

    /**
     * Get the global scopes for this class instance.
     *
     * @return array
     */
    public function getGlobalScopes() {
        /** @var CModel $this */
        return carr::get(static::$globalScopes, static::class, []);
    }
}
