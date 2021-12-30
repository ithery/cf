<?php
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Iterator\PathFilterIterator;

class CManager_Icon {
    private static $instance;

    /**
     * @var Collection
     */
    private $directories;

    /**
     * Previously processed icons.
     *
     * @var Collection
     */
    private $cache;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct() {
        $this->directories = c::collect();
        $this->cache = c::collect();
    }

    /**
     * @return CCollection
     */
    public function getDirectories() {
        return $this->directories;
    }

    /**
     * @param string $directory
     * @param string $prefix
     *
     * @return self
     */
    public function registerIconDirectory($prefix, $directory) {
        $this->directories = $this->directories->merge([
            $prefix => $directory,
        ]);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return null|string
     */
    public function loadFile($name) {
        if ($this->cache->has($name)) {
            return $this->cache->get($name);
        }

        $prefix = cstr::beforeLast($name, '.');
        $nameIcon = cstr::afterLast($name, '.') . '.svg';
        $dirs = $this->directories->get($prefix, $this->directories->toArray());

        $icons = $this->getFinder()->in($dirs);

        /** @var PathFilterIterator $iterator */
        $iterator = c::tap($icons->getIterator())
            ->rewind();

        /** @var null|SplFileInfo $file */
        $file = c::collect($iterator)
            ->filter(static function (SplFileInfo $file) use ($nameIcon) {
                return $file->getFilename() === $nameIcon;
            })->first();

        $icon = c::optional($file)->getContents();

        $this->cache->put($name, $icon);

        return $icon;
    }

    /**
     * @return Finder
     */
    protected function getFinder() {
        return (new Finder())
            ->ignoreUnreadableDirs()
            ->followLinks()
            ->ignoreDotFiles(true);
    }
}
