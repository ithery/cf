<?php
/**
 * @template TClass
 *
 * @property TClass $target
 */
class CBase_HigherOrderTapProxy {
    /**
     * The target being tapped.
     *
     * @var mixed
     */
    public $target;

    /**
     * Create a new tap proxy instance.
     *
     * @param TClass $target
     *
     * @return void
     */
    public function __construct($target) {
        $this->target = $target;
    }

    /**
     * Dynamically pass method calls to the target.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        $this->target->{$method}(...$parameters);

        return $this->target;
    }
}
