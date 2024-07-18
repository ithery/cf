<?php

class CConfig_Manager {
    protected $repository;

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct() {
        $this->repository = new CConfig_Repository([]);
    }

    public function load($group) {
        $items = CConfig_Loader::load($group);
        $this->repository->set($group, $items);
    }

    /**
     * @param array $items
     *
     * @return CConfig_Repository
     */
    public function newRepository(array $items) {
        $this->repository = new CConfig_Repository($items);

        return $this->repository;
    }

    /**
     * @return CConfig_Repository
     */
    public function repository() {
        return $this->repository;
    }
}
