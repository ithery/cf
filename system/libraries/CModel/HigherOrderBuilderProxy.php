<?php

/**
 * @mixin \CModel_Query
 */
class CModel_HigherOrderBuilderProxy {
    /**
     * The collection being operated on.
     *
     * @var \CModel_Query
     */
    protected $builder;

    /**
     * The method being proxied.
     *
     * @var string
     */
    protected $method;

    /**
     * Create a new proxy instance.
     *
     * @param \CModel_Query $builder
     * @param string        $method
     *
     * @return void
     */
    public function __construct(CModel_Query $builder, $method) {
        $this->method = $method;
        $this->builder = $builder;
    }

    /**
     * Proxy a scope call onto the query builder.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->builder->{$this->method}(function ($value) use ($method, $parameters) {
            return $value->{$method}(...$parameters);
        });
    }
}
