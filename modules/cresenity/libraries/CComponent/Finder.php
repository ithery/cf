<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
use Symfony\Component\Finder\SplFileInfo;

class CComponent_Finder {

    protected $path;
    protected $files;
    protected $manifest;
    protected $manifestPath;
    protected $registry;

    /**
     *
     * @var CComponent_Finder
     */
    protected static $instance;

    /**
     * 
     * @return CComponent_Finder
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function __construct() {

        $this->path = DOCROOT . 'application/cresenity/default/libraries/Cresenity/Component/';
        $this->manifestPath = DOCROOT . 'temp' . DS . 'component' . DS . 'manifest' . EXT;
        $this->registry = [];
        if (!CFile::isDirectory(dirname($this->manifestPath))) {

            CFile::makeDirectory(dirname($this->manifestPath), $mode = 0755, $recursive = true);
        }
    }

    public function registerComponent($alias, $class) {
        $this->registry[$alias] = $class;
    }

    public function find($alias) {
        return carr::get($this->getManifest(), $alias, null);
    }

    public function getManifest() {
        if (!is_null($this->manifest)) {
            return $this->manifest;
        }

        if (!file_exists($this->manifestPath)) {

            $this->build();
        }

        $this->manifest = CFile::getRequire($this->manifestPath);
        return array_merge($this->manifest, $this->registry);
    }

    public function build() {
        $this->manifest = $this->getClassNames()
                        ->mapWithKeys(function ($class) {
                            return [$class::getName() => $class];
                        })->toArray();

        $this->write($this->manifest);

        return $this;
    }

    protected function write(array $manifest) {
        if (!is_writable(dirname($this->manifestPath))) {
            throw new Exception('The ' . dirname($this->manifestPath) . ' directory must be present and writable.');
        }

        CFile::put($this->manifestPath, '<?php return ' . var_export($manifest, true) . ';', true);
    }

    public function getClassNames() {


        return c::collect(CFile::allFiles($this->path))
                        ->map(function (SplFileInfo $file) {

                            return
                                    c::str($file->getPathname())
                                    ->after(CF::appDir() . '/')
                                    ->replace(['/', '.php'], ['\\', ''])->__toString();
                        })
                        ->filter(function ($class) {
                            return is_subclass_of($class, CComponent::class) &&
                                    !(new ReflectionClass($class))->isAbstract();
                        });
    }

}
