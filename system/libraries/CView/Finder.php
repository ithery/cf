<?php

/**
 * Description of Finder.
 *
 * @author Hery
 */
class CView_Finder implements CView_Contract_ViewFinderInterface {
    /**
     * The array of active view paths.
     *
     * @var array
     */
    protected $paths;

    /**
     * The array of views that have been located.
     *
     * @var array
     */
    protected $views = [];

    /**
     * The namespace to file path hints.
     *
     * @var array
     */
    protected $hints = [];

    /**
     * Register a view extension with the finder.
     *
     * @var string[]
     */
    protected $extensions = ['blade.php', 'php', 'css', 'html'];

    /**
     * @var CView_Finder
     */
    private static $instance;

    /**
     * @return CView_Finder
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Create a new file view loader instance.
     *
     * @return void
     */
    public function __construct() {
        $this->paths = [];
    }

    /**
     * Get the fully qualified location of the view.
     *
     * @param string $name
     *
     * @return string
     */
    public function find($name) {
        if (isset($this->views[$name])) {
            return $this->views[$name];
        }

        if ($this->hasHintInformation($name = trim($name))) {
            return $this->views[$name] = $this->findNamespacedView($name);
        }

        return $this->views[$name] = $this->findInPaths($name, $this->paths);
    }

    /**
     * Get the path to a template with a named path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function findNamespacedView($name) {
        list($namespace, $view) = $this->parseNamespaceSegments($name);

        return $this->findInPaths($view, $this->hints[$namespace]);
    }

    /**
     * Get the segments of a template with a named path.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function parseNamespaceSegments($name) {
        $segments = explode(CView::HINT_PATH_DELIMITER, $name);

        if (count($segments) !== 2) {
            throw new InvalidArgumentException("View [{$name}] has an invalid name.");
        }

        if (!isset($this->hints[$segments[0]])) {
            throw new InvalidArgumentException("No hint path defined for [{$segments[0]}].");
        }

        return $segments;
    }

    /**
     * Find the given view in the list of paths.
     *
     * @param string $name
     * @param array  $paths
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function findInPaths($name, $paths) {
        $cfPath = CF::getDirs(CView::VIEW_FOLDER, null, false);

        $paths = array_merge($cfPath, $paths);

        foreach ((array) $paths as $path) {
            foreach ($this->getPossibleViewFiles($name) as $file) {
                if (file_exists($viewPath = c::untrailingslashit($path) . '/' . $file)) {
                    return $viewPath;
                }
            }
        }

        throw new InvalidArgumentException("View [{$name}] not found.");
    }

    /**
     * Get an array of possible view files.
     *
     * @param string $name
     *
     * @return array
     */
    protected function getPossibleViewFiles($name) {
        return array_map(function ($extension) use ($name) {
            return str_replace('.', '/', $name) . '.' . $extension;
        }, $this->extensions);
    }

    /**
     * Add a location to the finder.
     *
     * @param string $location
     *
     * @return void
     */
    public function addLocation($location) {
        $this->paths[] = $this->resolvePath($location);
    }

    /**
     * Prepend a location to the finder.
     *
     * @param string $location
     *
     * @return void
     */
    public function prependLocation($location) {
        array_unshift($this->paths, $this->resolvePath($location));
    }

    /**
     * Resolve the path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function resolvePath($path) {
        return realpath($path) ?: $path;
    }

    /**
     * Add a namespace hint to the finder.
     *
     * @param string       $namespace
     * @param string|array $hints
     *
     * @return void
     */
    public function addNamespace($namespace, $hints) {
        $hints = (array) $hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($this->hints[$namespace], $hints);
        }

        $this->hints[$namespace] = $hints;
    }

    /**
     * Prepend a namespace hint to the finder.
     *
     * @param string       $namespace
     * @param string|array $hints
     *
     * @return void
     */
    public function prependNamespace($namespace, $hints) {
        $hints = (array) $hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($hints, $this->hints[$namespace]);
        }

        $this->hints[$namespace] = $hints;
    }

    /**
     * Replace the namespace hints for the given namespace.
     *
     * @param string       $namespace
     * @param string|array $hints
     *
     * @return void
     */
    public function replaceNamespace($namespace, $hints) {
        $this->hints[$namespace] = (array) $hints;
    }

    /**
     * Register an extension with the view finder.
     *
     * @param string $extension
     *
     * @return void
     */
    public function addExtension($extension) {
        if (($index = array_search($extension, $this->extensions)) !== false) {
            unset($this->extensions[$index]);
        }

        array_unshift($this->extensions, $extension);
    }

    /**
     * Returns whether or not the view name has any hint information.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasHintInformation($name) {
        return strpos($name, CView::HINT_PATH_DELIMITER) > 0;
    }

    /**
     * Flush the cache of located views.
     *
     * @return void
     */
    public function flush() {
        $this->views = [];
    }

    /**
     * Set the active view paths.
     *
     * @param array $paths
     *
     * @return $this
     */
    public function setPaths($paths) {
        $this->paths = $paths;

        return $this;
    }

    /**
     * Get the active view paths.
     *
     * @return array
     */
    public function getPaths() {
        return $this->paths;
    }

    /**
     * Get the views that have been located.
     *
     * @return array
     */
    public function getViews() {
        return $this->views;
    }

    /**
     * Get the namespace to file path hints.
     *
     * @return array
     */
    public function getHints() {
        return $this->hints;
    }

    /**
     * Get registered extensions.
     *
     * @return array
     */
    public function getExtensions() {
        return $this->extensions;
    }
}
