<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 14, 2019, 11:54:53 AM
 */
class CTranslation_Loader_FileLoader extends CTranslation_LoaderAbstract {
    /**
     * The filesystem instance.
     *
     * @var CFile
     */
    protected $files;

    /**
     * The default path for the loader.
     *
     * @var string
     */
    protected $path;

    /**
     * All of the registered paths to JSON translation files.
     *
     * @var string
     */
    protected $jsonPaths = [];

    /**
     * All of the namespace hints.
     *
     * @var array
     */
    protected $hints = [];

    /**
     * Create a new file loader instance.
     *
     * @param CFile  $files
     * @param string $path
     *
     * @return void
     */
    public function __construct(CFile $files, $path) {
        $this->path = $path;
        $this->files = $files;
    }

    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null) {
        if ($group == '*' && $namespace == '*') {
            return $this->loadJsonPaths($locale);
        }

        if (is_null($namespace) || $namespace == '*') {
            return $this->loadPath($this->path, $locale, $group);
        }

        return $this->loadNamespaced($locale, $group, $namespace);
    }

    /**
     * Load a namespaced translation group.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    protected function loadNamespaced($locale, $group, $namespace) {
        if (isset($this->hints[$namespace])) {
            $lines = $this->loadPath($this->hints[$namespace], $locale, $group);

            return $this->loadNamespaceOverrides($lines, $locale, $group, $namespace);
        }

        return [];
    }

    /**
     * Load a local namespaced translation group for overrides.
     *
     * @param array  $lines
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    protected function loadNamespaceOverrides(array $lines, $locale, $group, $namespace) {
        $file = "{$this->path}/vendor/{$namespace}/{$locale}/{$group}.php";

        if ($this->files->exists($file)) {
            return array_replace_recursive($lines, $this->files->getRequire($file));
        }

        return $lines;
    }

    /**
     * Load a locale from a given path.
     *
     * @param string $path
     * @param string $locale
     * @param string $group
     *
     * @return array
     */
    protected function loadPath($path, $locale, $group) {
        $cfPaths = CF::paths();
        $result = [];
        $cfPaths = array_reverse($cfPaths);
        foreach ($cfPaths as $cfPath) {
            //remove docroot when started with DOCROOT
            if (cstr::startsWith($path, DOCROOT)) {
                $path = substr($path, strlen(DOCROOT));
            }
            $pathToFind = $cfPath . $path;

            if ($this->files->exists($full = "{$pathToFind}/{$locale}/{$group}.php")) {
                $array = $this->files->getRequire($full);
                if (is_array($array)) {
                    $result = array_merge($result, $array);
                }
            }
        }

        return $result;
    }

    /**
     * Load a locale from the given JSON file path.
     *
     * @param string $locale
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    protected function loadJsonPaths($locale) {
        return c::collect(array_merge($this->jsonPaths, [$this->path]))
            ->reduce(function ($output, $path) use ($locale) {
                if ($this->files->exists($full = "{$path}/{$locale}.json")) {
                    $decoded = json_decode($this->files->get($full), true);

                    if (is_null($decoded) || json_last_error() !== JSON_ERROR_NONE) {
                        throw new RuntimeException("Translation file [{$full}] contains an invalid JSON structure.");
                    }

                    $output = array_merge($output, $decoded);
                }

                return $output;
            }, []);
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param string $namespace
     * @param string $hint
     *
     * @return void
     */
    public function addNamespace($namespace, $hint) {
        $this->hints[$namespace] = $hint;
    }

    /**
     * Add a new JSON path to the loader.
     *
     * @param string $path
     *
     * @return void
     */
    public function addJsonPath($path) {
        $this->jsonPaths[] = $path;
    }

    /**
     * Get an array of all the registered namespaces.
     *
     * @return array
     */
    public function namespaces() {
        return $this->hints;
    }
}
