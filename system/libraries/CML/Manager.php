<?php

class CML_Manager {
    protected static $instance;

    protected $defaultAdapter = 'rubix';

    protected $modelRepositories = [];

    public static function getDefaultModelPath() {
        return  CF::config('ml.ai_model_path_output', DOCROOT . 'temp/ml/' . CF::appCode() . '/model/');
    }

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return CML_Adapter_RubixAdapter
     */
    public function createRubixAdapter() {
        return new CML_Adapter_RubixAdapter();
    }

    /**
     * @param string $adapter
     *
     * @return CML_AdapterAbstract
     */
    protected function createAdapter($adapter) {
        $adapterMap = [
            'rubix' => CML_Adapter_RubixAdapter::class
        ];
        $adapterClass = carr::get($adapterMap, $adapter);

        return new $adapterClass();
    }

    /**
     * @param null|string $adapter
     *
     * @return CML_AdapterAbstract
     */
    public function adapter($adapter = null) {
        if ($adapter == null) {
            $adapter = $this->defaultAdapter;
        }
        $adapter = $this->createAdapter($adapter);

        return $adapter;
    }

    /**
     * @param null|string $path
     *
     * @return CML_ModelRepository
     */
    public function getModelRepository($path = null) {
        if ($path == null) {
            $path = $this->getDefaultModelPath();
        }
        if (!isset($this->modelRepositories[$path])) {
            $this->modelRepositories[$path] = new CML_ModelRepository($path);
        }

        return $this->modelRepositories[$path];
    }
}
