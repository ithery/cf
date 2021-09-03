<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */


trait CComponent_Concern_HandlesActionsTrait
{
    public function syncInput($name, $value, $rehash = true)
    {
        $propertyName = $this->beforeFirstDot($name);

        c::throwIf(
            ($this->{$propertyName} instanceof CModel || $this->{$propertyName} instanceof CModel_Collection) && $this->missingRuleFor($name),
            new CComponent_Exception_CannotBindToModelDataWithoutValidationRuleException($name, $this::getName())
        );

        $this->callBeforeAndAfterSyncHooks($name, $value, function ($name, $value) use ($propertyName, $rehash) {
            c::throwUnless(
                $this->propertyIsPublicAndNotDefinedOnBaseClass($propertyName),
                new CComponent_Exception_PublicPropertyNotFoundException($propertyName, $this::getName())
            );

            if ($this->containsDots($name)) {
                // Strip away model name.
                $keyName = $this->afterFirstDot($name);
                // Get model attribute to be filled.
                $targetKey = $this->beforeFirstDot($keyName);

                // Get existing data from model property.
                $results = [];
                $results[$targetKey] = CF::get($this->{$propertyName}, $targetKey, []);

                // Merge in new data.
                CF::set($results, $keyName, $value);

                // Re-assign data to model.
                CF::set($this->{$propertyName}, $targetKey, $results[$targetKey]);
            } else {
                $this->{$name} = $value;
            }

            $rehash && CComponent_HydrationMiddleware_HashDataPropertiesForDirtyDetection::rehashProperty($name, $value, $this);
        });
    }

    protected function callBeforeAndAfterSyncHooks($name, $value, $callback)
    {
        $name = c::str($name);

        $propertyName = $name->studly()->before('.');
        $keyAfterFirstDot = $name->contains('.') ? $name->after('.') : null;
        $keyAfterLastDot = $name->contains('.') ? $name->afterLast('.') : null;

        $beforeMethod = 'updating'.$propertyName;
        $afterMethod = 'updated'.$propertyName;

        $beforeNestedMethod = $name->contains('.')
            ? 'updating'.$name->replace('.', '_')->studly()
            : false;

        $afterNestedMethod = $name->contains('.')
            ? 'updated'.$name->replace('.', '_')->studly()
            : false;

        $name = $name->__toString();

        $this->updating($name, $value);

        if (method_exists($this, $beforeMethod)) {
            $this->{$beforeMethod}($value, $keyAfterFirstDot);
        }

        if ($beforeNestedMethod && method_exists($this, $beforeNestedMethod)) {
            $this->{$beforeNestedMethod}($value, $keyAfterLastDot);
        }

        CComponent_Manager::instance()->dispatch('component.updating', $this, $name, $value);

        $callback($name, $value);

        $this->updated($name, $value);

        if (method_exists($this, $afterMethod)) {
            $this->{$afterMethod}($value, $keyAfterFirstDot);
        }

        if ($afterNestedMethod && method_exists($this, $afterNestedMethod)) {
            $this->{$afterNestedMethod}($value, $keyAfterLastDot);
        }

        CComponent_Manager::instance()->dispatch('component.updated', $this, $name, $value);
    }

    public function callMethod($method, $params = [])
    {
        switch ($method) {
            case '$sync':
                $prop = array_shift($params);
                $this->syncInput($prop, head($params));

                return;

            case '$set':
                $prop = array_shift($params);
                $this->syncInput($prop, carr::head($params), $rehash = false);

                return;

            case '$toggle':
                $prop = array_shift($params);
                $this->syncInput($prop, ! $this->{$prop}, $rehash = false);

                return;

            case '$refresh':
                return;
        }

        if (! method_exists($this, $method)) {
            c::throwIf($method === 'startUpload', new CComponent_Exception_MissingFileUploadsTraitException($this));

            throw new CComponent_Exception_MethodNotFoundException($method, $this::getName());
        }

        c::throwUnless($this->methodIsPublicAndNotDefinedOnBaseClass($method), new CComponent_Exception_NonPublicComponentMethodCall($method));

        $returned = CComponent_ImplicitlyBoundMethod::call(CContainer::getInstance(), [$this, $method], $params);

        CComponent_Manager::instance()->dispatch('action.returned', $this, $method, $returned);
    }

    protected function methodIsPublicAndNotDefinedOnBaseClass($methodName)
    {
        return c::collect((new \ReflectionClass($this))->getMethods(\ReflectionMethod::IS_PUBLIC))
            ->reject(function ($method) {
                // The "render" method is a special case. This method might be called by event listeners or other ways.
                if ($method === 'render') {
                    return false;
                }

                return $method->class === self::class;
            })
            ->pluck('name')
            ->search($methodName) !== false;
    }
}