<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 30, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_TemporaryUploadedFile extends CHTTP_UploadedFile {

    protected $storage;
    protected $path;

    public function __construct($path, $disk) {
        $this->disk = $disk;
        $this->storage = CStorage::instance()->disk($this->disk);
        $this->path = CComponent_FileUploadConfiguration::path($path, false);

        $tmpFile = tmpfile();

        parent::__construct(stream_get_meta_data($tmpFile)['uri'], $this->path);
    }

    public function isValid() {
        return true;
    }

    public function getSize() {
        if (CF::isTesting() && c::str($this->getfilename())->contains('-size=')) {
            return (int) c::str($this->getFilename())->between('-size=', '.')->__toString();
        }

        return (int) $this->storage->size($this->path);
    }

    public function getMimeType() {
        return $this->storage->mimeType($this->path);
    }

    public function getFilename() {
        return $this->getName($this->path);
    }

    public function getRealPath() {
        return $this->storage->path($this->path);
    }

    public function getClientOriginalName() {
        return $this->extractOriginalNameFromFilePath($this->path);
    }

    public function temporaryUrl() {
        if (CComponent_FileUploadConfiguration::isUsingS3() && !CF::isTesting()) {
            return $this->storage->temporaryUrl(
                            $this->path,
                            c::now()->addDay(),
                            ['ResponseContentDisposition' => 'filename="' . $this->getClientOriginalName() . '"']
            );
        }

        $supportedPreviewTypes = CF::config('component.temporary_file_upload.preview_mimes', [
                    'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
                    'mov', 'avi', 'wmv', 'mp3', 'm4a',
                    'jpeg', 'mpga', 'webp', 'wma',
        ]);

        if (!in_array($this->guessExtension(), $supportedPreviewTypes)) {
            // This will throw an error because it's not used with S3.
            return $this->storage->temporaryUrl($this->path, c::now()->addDay());
        }

        //TODO: get from route
        /**
          return c::url()->temporarySignedRoute(
          'component.preview-file', c::now()->addMinutes(30), ['filename' => $this->getFilename()]
          );
         */
        return curl::base() . 'cresenity/component/preview?filename=' . $this->getFilename();
    }

    public function readStream() {
        return $this->storage->readStream($this->path);
    }

    public function exists() {
        return $this->storage->exists($this->path);
    }

    public function get() {
        return $this->storage->get($this->path);
    }

    public function delete() {
        return $this->storage->delete($this->path);
    }

    public function storeAs($path, $name, $options = []) {
        $options = $this->parseOptions($options);

        $disk = carr::pull($options, 'disk') ?: $this->disk;

        
        $newPath = trim($path . '/' . $name, '/');
       
        CStorage::instance()->disk($disk)->put(
                $newPath, $this->storage->readStream($this->path), $options
        );

        return $newPath;
    }

    public static function generateHashNameWithOriginalNameEmbedded($file) {
        $hash = c::str()->random(30);
        $meta = c::str('-meta' . base64_encode($file->getClientOriginalName()) . '-')->replace('/', '_');
        $extension = '.' . $file->guessExtension();

        return $hash . $meta . $extension;
    }

    public function extractOriginalNameFromFilePath($path) {
        return base64_decode(head(explode('-', last(explode('-meta', str($path)->replace('_', '/'))))));
    }

    public static function createFromComponent($filePath) {
        return new static($filePath, CComponent_FileUploadConfiguration::disk());
    }

    public static function canUnserialize($subject) {
        if (is_string($subject)) {
            return (string) c::str($subject)->startsWith(['component-file:', 'component-files:']);
        }

        if (is_array($subject)) {
            return c::collect($subject)->contains(function ($value) {
                        return static::canUnserialize($value);
                    });
        }

        return false;
    }

    public static function unserializeFromComponentRequest($subject) {
        if (is_string($subject)) {
            if (c::str($subject)->startsWith('component-file:')) {
                return static::createFromComponent(c::str($subject)->after('component-file:'));
            }

            if (str($subject)->startsWith('component-files:')) {
                $paths = json_decode(str($subject)->after('component-files:'), true);

                return c::collect($paths)->map(function ($path) {
                            return static::createFromComponent($path);
                        })->toArray();
            }

            return $subject;
        }

        if (is_array($subject)) {
            foreach ($subject as $key => $value) {
                $subject[$key] = static::unserializeFromComponentRequest($value);
            }

            return $subject;
        }
    }

    public function serializeForComponentResponse() {
        return 'component-file:' . $this->getFilename();
    }

    public static function serializeMultipleForComponentResponse($files) {
        return 'component-files:' . json_encode(collect($files)->map->getFilename());
    }

}
