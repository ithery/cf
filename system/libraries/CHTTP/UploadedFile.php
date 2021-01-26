<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 *
 * @since Aug 11, 2019, 10:38:55 PM
 *
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class CHTTP_UploadedFile extends SymfonyUploadedFile {
    use CHTTP_Trait_FileHelpersTrait,
        CTrait_Macroable;

    /**
     * Begin creating a new file fake.
     *
     * @return CHTTP_Testing_FileFactory
     */
    public static function fake() {
        return new CHTTP_Testing_FileFactory;
    }

    /**
     * Store the uploaded file on a filesystem disk.
     *
     * @param string       $path
     * @param array|string $options
     *
     * @return string|false
     */
    public function store($path, $options = []) {
        return $this->storeAs($path, $this->hashName(), $this->parseOptions($options));
    }

    /**
     * Store the uploaded file on a filesystem disk with public visibility.
     *
     * @param string       $path
     * @param array|string $options
     *
     * @return string|false
     */
    public function storePublicly($path, $options = []) {
        $options = $this->parseOptions($options);
        $options['visibility'] = 'public';
        return $this->storeAs($path, $this->hashName(), $options);
    }

    /**
     * Store the uploaded file on a filesystem disk with public visibility.
     *
     * @param string       $path
     * @param string       $name
     * @param array|string $options
     *
     * @return string|false
     */
    public function storePubliclyAs($path, $name, $options = []) {
        $options = $this->parseOptions($options);
        $options['visibility'] = 'public';
        return $this->storeAs($path, $name, $options);
    }

    /**
     * Store the uploaded file on a filesystem disk.
     *
     * @param string       $path
     * @param string       $name
     * @param array|string $options
     *
     * @return string|false
     */
    public function storeAs($path, $name, $options = []) {
        $options = $this->parseOptions($options);
        $disk = carr::pull($options, 'disk');

        return CStorage::instance()->disk($disk)->putFileAs(
            $path,
            $this,
            $name,
            $options
        );
    }

    /**
     * Get the contents of the uploaded file.
     *
     * @return bool|string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get() {
        if (!$this->isValid()) {
            throw new CStorage_Exception_FileNotFoundException("File does not exist at path {$this->getPathname()}");
        }
        return file_get_contents($this->getPathname());
    }

    /**
     * Get the file's extension supplied by the client.
     *
     * @return string
     */
    public function clientExtension() {
        return $this->guessClientExtension();
    }

    /**
     * Create a new file instance from a base instance.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param bool                                                $test
     *
     * @return static
     */
    public static function createFromBase(SymfonyUploadedFile $file, $test = false) {
        return $file instanceof static ? $file : new static(
            $file->getPathname(),
            $file->getClientOriginalName(),
            $file->getClientMimeType(),
            $file->getError(),
            $test
        );
    }

    /**
     * Parse and format the given options.
     *
     * @param array|string $options
     *
     * @return array
     */
    protected function parseOptions($options) {
        if (is_string($options)) {
            $options = ['disk' => $options];
        }
        return $options;
    }
}
