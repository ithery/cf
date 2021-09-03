<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 20, 2018, 12:21:47 AM
 */

/**
 * A registry for templates.
 */
class CTemplate_Registry {
    /**
     * The map of explicit template names and locations.
     *
     * @var array
     */
    protected $map = [];

    /**
     * The paths to search for implicit template names.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * Templates found in the search paths.
     *
     * @var array
     */
    protected $found = [];

    /**
     * File extension to use when searching the path list for templates.
     *
     * @var string
     */
    protected $templateFileExtension = '.php';

    /**
     * Constructor.
     *
     * @param array $map   a map of explicit template names and locations
     * @param array $paths a map of filesystem paths to search for templates
     */
    public function __construct(array $map = [], array $paths = []) {
        foreach ($map as $name => $spec) {
            $this->set($name, $spec);
        }
        $this->setPaths($paths);
    }

    /**
     * Registers a template.
     *
     * If the template is a string, it is treated as a path to a PHP include
     * file, and gets wrapped inside a closure that includes that file.
     * Otherwise the template is treated as a callable.
     *
     * @param string          $name register the template under this name
     * @param string|callable $spec a string path to a PHP include file, or a
     *                              callable
     *
     * @return null
     */
    public function set($name, $spec) {
        if (is_string($spec)) {
            $spec = $this->enclose($spec);
        }
        $this->map[$name] = $spec;
    }

    /**
     * Is a named template registered?
     *
     * @param string $name the template name
     *
     * @return bool
     */
    public function has($name) {
        return isset($this->map[$name]) || $this->find($name);
    }

    /**
     * Gets a template from the registry.
     *
     * @param string $name the template name
     *
     * @return \Closure
     */
    public function get($name) {
        if (isset($this->map[$name])) {
            return $this->map[$name];
        }
        if ($this->find($name)) {
            return $this->found[$name];
        }
        throw new CTemplate_Exception_TemplateNotFound($name);
    }

    /**
     * Gets a copy of the current search paths.
     *
     * @return array
     */
    public function getPaths() {
        return $this->paths;
    }

    /**
     * Adds one path to the top of the search paths.
     *
     *     $registry->prependPath('/path/1');
     *     $registry->prependPath('/path/2');
     *     $registry->prependPath('/path/3');
     *     // $this->getPaths() reveals that the directory search
     *     // order will be '/path/3/', '/path/2/', '/path/1/'.
     *
     * @param array|string $path the directories to add to the paths
     *
     * @return null
     */
    public function prependPath($path) {
        array_unshift($this->paths, rtrim($path, DIRECTORY_SEPARATOR));
        $this->found = [];
    }

    /**
     * Adds one path to the end of the search paths.
     *
     *     $registry->appendPath('/path/1');
     *     $registry->appendPath('/path/2');
     *     $registry->appendPath('/path/3');
     *     // $registry->getPaths() reveals that the directory search
     *     // order will be '/path/1/', '/path/2/', '/path/3/'.
     *
     * @param array|string $path the directories to add to the paths
     *
     * @return null
     */
    public function appendPath($path) {
        $this->paths[] = rtrim($path, DIRECTORY_SEPARATOR);
        $this->found = [];
    }

    /**
     * Sets the paths directly.
     *
     *      $registry->setPaths([
     *          '/path/1',
     *          '/path/2',
     *          '/path/3',
     *      ]);
     *      // $registry->getPaths() reveals that the search order will
     *      // be '/path/1', '/path/2', '/path/3'.
     *
     * @param array $paths the paths to set
     *
     * @return null
     */
    public function setPaths(array $paths) {
        $this->paths = $paths;
        $this->found = [];
    }

    /**
     * Sets the extension to be used when searching for templates via find().
     *
     * @param string $templateFileExtension
     *
     * @return null
     */
    public function setTemplateFileExtension($templateFileExtension) {
        $this->templateFileExtension = $templateFileExtension;
    }

    /**
     * Finds a template in the search paths.
     *
     * @param string $name the template name
     *
     * @return bool true if found, false if not
     */
    protected function find($name) {
        if (isset($this->found[$name])) {
            return true;
        }
        foreach ($this->paths as $path) {
            $file = $path . DIRECTORY_SEPARATOR . $name . $this->templateFileExtension;
            if ($this->isReadable($file)) {
                $this->found[$name] = $this->enclose($file);
                return true;
            }
        }
        return false;
    }

    /**
     * Checks to see if a file is readable.
     *
     * @param string $file the file to find
     *
     * @return bool
     */
    protected function isReadable($file) {
        return is_readable($file);
    }

    /**
     * Wraps a template file name in a Closure.
     *
     * @param string $__FILE__ the file name
     *
     * @return \Closure
     */
    protected function enclose($__FILE__) {
        return function (array $__VARS__ = []) use ($__FILE__) {
            extract($__VARS__, EXTR_SKIP);
            $path = $__FILE__;
            if (cstr::endsWith($__FILE__, '.blade.php')) {
                $compiler = CView_Compiler_BladeCompiler::instance();
                if ($compiler->isExpired($path)) {
                    $compiler->compile($path);
                }
                $path = $compiler->getCompiledPath($path);
            }
            require $path;
        };
    }
}
