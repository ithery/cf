<?php

class CBase_MemoizeResolver {
    public $cache;
    private $resolver;
    private $func;

    /**
     * @param Closure $func
     * @param Closure $resolver
     */
    public function __construct($func, $resolver = null) {
        $this->resolver = $resolver;
        $this->func = $func;
    }

    public function __invoke() {
        $args = \func_get_args();
        if ($this->resolver) {
            $closure = Closure::bind($this->resolver, $this);
            //$closure = Closure::fromCallable($this->resolver)->bindTo($this);
            $key = $closure(...$args);
        } else {
            $key = &$args[0];
        }
        $cache = $this->cache;
        if ($cache->has($key)) {
            return $cache->get($key);
        }
        $func = $this->func;
        $result = $func(...$args);
        $this->cache = $this->cache->set($key, $result);
        return $result;
    }
}
