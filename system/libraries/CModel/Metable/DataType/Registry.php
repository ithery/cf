<?php

/**
 * List of available data type Handlers.
 */
class CModel_Metable_DataType_Registry {
    /**
     * List of registered handlers .
     *
     * @var array
     */
    protected $handlers = [];

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __construct() {
        foreach (CF::config('model.metable.datatypes', []) as $handler) {
            $this->addHandler(new $handler());
        }
    }

    /**
     * Append a Handler to use for a given type identifier.
     *
     * @param CModel_Metable_DataType_HandlerInterface $handler
     *
     * @return void
     */
    public function addHandler(CModel_Metable_DataType_HandlerInterface $handler) {
        $this->handlers[$handler->getDataType()] = $handler;
    }

    /**
     * Retrieve the handler assigned to a given type identifier.
     *
     * @param string $type
     *
     * @throws CModel_Metable_Exception_DataTypeException if no handler is found
     *
     * @return CModel_Metable_DataType_HandlerInterface
     */
    public function getHandlerForType(string $type) {
        if ($this->hasHandlerForType($type)) {
            return $this->handlers[$type];
        }

        throw CModel_Metable_Exception_DataTypeException::handlerNotFound($type);
    }

    /**
     * Check if a handler has been set for a given type identifier.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasHandlerForType($type) {
        return array_key_exists($type, $this->handlers);
    }

    /**
     * Removes the handler with a given type identifier.
     *
     * @param string $type
     *
     * @return void
     */
    public function removeHandlerForType($type) {
        unset($this->handlers[$type]);
    }

    /**
     * Find a data type Handler that is able to operate on the value, return the type identifier associated with it.
     *
     * @param mixed $value
     *
     * @throws CModel_Metable_Exception_DataTypeException if no handler can handle the value
     *
     * @return string
     */
    public function getTypeForValue($value) {
        foreach ($this->handlers as $type => $handler) {
            if ($handler->canHandleValue($value)) {
                return $type;
            }
        }

        throw CModel_Metable_Exception_DataTypeException::handlerNotFoundForValue($value);
    }
}
