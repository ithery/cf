<?php

/**
 * Description of Factory
 *
 * @author Hery
 */
class CView_Factory {

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function __construct() {
        
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  CInterface_Arrayable|array  $data
     * @param  array  $mergeData
     * @return CView_View
     */
    public function make($view, $data = [], $mergeData = []) {
        $path = $this->finder->find(
                $view = $this->normalizeName($view)
        );

        // Next, we will create the view instance and call the view creator for the view
        // which can set any data, etc. Then we will return the view instance back to
        // the caller for rendering or performing other view manipulations on this.
        $data = array_merge($mergeData, $this->parseData($data));

        return CF::tap($this->viewInstance($view, $path, $data), function ($view) {
                    $this->callCreator($view);
                });
    }

    /**
     * Normalize a view name.
     *
     * @param  string  $name
     * @return string
     */
    protected function normalizeName($name) {
        return CView_Helper::normalize($name);
    }

    /**
     * Create a new view instance from the given arguments.
     *
     * @param  string  $view
     * @param  string  $path
     * @param  CInterface_Arrayable|array  $data
     * @return CView_View
     */
    protected function viewInstance($view, $path, $data) {
        return new CView_View($this, $this->getEngineFromPath($path), $view, $path, $data);
    }

    /**
     * Get the appropriate view engine for the given path.
     *
     * @param  string  $path
     * @return CView_EngineAbstract
     *
     * @throws \InvalidArgumentException
     */
    public function getEngineFromPath($path) {
        if (!$extension = $this->getExtension($path)) {
            throw new InvalidArgumentException("Unrecognized extension in file: {$path}.");
        }

        $engine = $this->extensions[$extension];

        return $this->engines->resolve($engine);
    }

    /**
     * Get the extension used by the view file.
     *
     * @param  string  $path
     * @return string|null
     */
    protected function getExtension($path) {
        $extensions = array_keys($this->extensions);

        return carr::first($extensions, function ($value) use ($path) {
                    return cstr::endsWith($path, '.' . $value);
                });
    }

}
