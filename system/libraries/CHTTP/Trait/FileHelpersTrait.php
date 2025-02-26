<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 10:50:01 PM
 */
trait CHTTP_Trait_FileHelpersTrait {
    /**
     * The cache copy of the file's hash name.
     *
     * @var string
     */
    protected $hashName = null;

    /**
     * Get the fully qualified path to the file.
     *
     * @return string
     */
    public function path() {
        return $this->getRealPath();
    }

    /**
     * Get the file's extension.
     *
     * @return string
     */
    public function extension() {
        return $this->guessExtension();
    }

    /**
     * Get a filename for the file.
     *
     * @param null|string $path
     *
     * @return string
     */
    public function hashName($path = null) {
        if ($path) {
            $path = rtrim($path, '/') . '/';
        }
        $hash = $this->hashName ?: $this->hashName = cstr::random(40);
        if ($extension = $this->guessExtension()) {
            $extension = '.' . $extension;
        }

        return $path . $hash . $extension;
    }
}
