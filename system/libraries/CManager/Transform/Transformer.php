<?php

class CManager_Transform_Transformer {
    /**
     * The transform method that may be applied to date.
     *
     * @var array
     */
    protected $dateMethod = [
        'FormatDate', 'UnformatDate', 'FormatDatetime', 'UnformatDatetime'
    ];

    protected $methods;

    public function __construct($methods) {
        $this->setMethods($methods);
    }

    public function setMethods($methods) {
        $this->methods = [];

        $this->addMethods($methods);

        return $this;
    }

    /**
     * Parse the given methods and merge them into current rules.
     *
     * @param array $methods
     *
     * @return void
     */
    public function addMethods($methods) {
        // The primary purpose of this parser is to expand any "*" rules to the all
        // of the explicit rules needed for the given data. For example the rule
        // names.* would get expanded to names.0, names.1, etc. for this data.
        $methods = carr::wrap($methods);

        $this->methods = array_merge_recursive(
            $this->methods,
            $methods
        );
    }

    public function transform($value, array $data = []) {
        foreach ($this->methods as $method) {
            $value = $this->transformMethod($method, $value, $data);
            if ($this->shouldStopTransforming($method, $value)) {
                break;
            }
        }

        return $value;
    }

    protected function transformMethod($method, $value, array $data = []) {
        list($method, $parameters) = CManager_Transform_Parser::parse($method);
        $transformable = $this->isTransformable($method, $value);

        $arguments = CManager_Transform_Parser::getArguments($parameters, $data);
        if ($transformable) {
            $methodExecutor = new CManager_Transform_MethodExecutor($method);

            return $methodExecutor->transform($value, $arguments);
        }

        return $value;
    }

    /**
     * Check if we should stop further transforming on a given method and value.
     *
     * @param string $method
     * @param mixed  $value
     *
     * @return bool
     */
    protected function shouldStopTransforming($method, $value) {
        return false;
    }

    /**
     * Determine if the method is transformable.
     *
     * @param string $method
     * @param mixed  $value
     *
     * @return bool
     */
    public static function isTransformable($method, $value = null) {
        if ($method instanceof CManager_Transform_Contract_TransformMethodInterface) {
            return true;
        }

        if (is_string($method)) {
            list($method, $arguments) = CManager_Transform_Parser::parse($method);
            $method = CManager_Transform_Parser::normalizeMethod($method);

            $methodExecutorMethod = 'transform' . $method;
            if (method_exists(CManager_Transform_MethodExecutor::class, $methodExecutorMethod)) {
                return true;
            }
        }

        return true;
    }
}
