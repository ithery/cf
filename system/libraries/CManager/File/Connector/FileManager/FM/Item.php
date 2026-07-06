<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Connector_FileManager_FM_Item {
    /**
     * Lazily-filled cache of computed attributes, keyed by column name. See __get()/fill().
     *
     * @var array
     */
    public $attributes = [];

    /**
     * @var CManager_File_Connector_FileManager_FM_Path
     */
    private $fmPath;

    /**
     * @var CManager_File_Connector_FileManager_FM
     */
    private $helper;

    /**
     * Attribute names exposed by fill(), each mapped to a same-named (camelCased) method.
     * 'size_bytes' (-> sizeBytes()) is included alongside 'size' so the client can
     * re-sort the File Size column itself (numerically) without an ajax round-trip --
     * see FileManager.js's sortItems().
     *
     * @var string[]
     */
    private $columns = ['name', 'url', 'time', 'icon', 'is_file', 'is_image', 'mime_type', 'thumb_url', 'size', 'size_bytes'];

    /**
     * @param CManager_File_Connector_FileManager_FM_Path $fmPath
     * @param CManager_File_Connector_FileManager_FM      $helper
     *
     * @return void
     */
    public function __construct(CManager_File_Connector_FileManager_FM_Path $fmPath, CManager_File_Connector_FileManager_FM $helper) {
        $this->fmPath = $fmPath->thumb(false);
        $this->helper = $helper;
    }

    /**
     * Lazily computes and caches the given attribute by calling its matching
     * camelCase method (e.g. 'is_image' -> isImage()).
     *
     * @param string $var_name
     *
     * @return mixed
     */
    public function __get($var_name) {
        if (!array_key_exists($var_name, $this->attributes)) {
            $function_name = cstr::camel($var_name);
            $this->attributes[$var_name] = $this->$function_name();
        }

        return $this->attributes[$var_name];
    }

    /**
     * Populate $attributes with every column in $columns.
     *
     * @return $this
     */
    public function fill() {
        foreach ($this->columns as $column) {
            $this->__get($column);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function name() {
        return $this->fmPath->getName();
    }

    /**
     * @param string $type see CManager_File_Connector_FileManager_FM_Path::path()
     *
     * @return string
     */
    public function path($type = 'absolute') {
        return $this->fmPath->path($type);
    }

    /**
     * @return bool
     */
    public function isDirectory() {
        return $this->fmPath->isDirectory();
    }

    /**
     * @return bool
     */
    public function isFile() {
        return !$this->isDirectory();
    }

    /**
     * Check a file is image or not.
     *
     * @return bool
     */
    public function isImage() {
        if (!$this->isDirectory()) {
            return cstr::startsWith($this->mimeType(), 'image');
        }

        return false;
    }

    /**
     * Get mime type of a file. Empty string for directories -- mime type isn't a
     * meaningful concept for them and Flysystem throws trying to read it.
     *
     * @return string
     */
    public function mimeType() {
        if ($this->isDirectory()) {
            return '';
        }

        // TODO: uploaded file
        // if ($file instanceof UploadedFile) {
        //     return $file->getMimeType();
        // }
        return $this->fmPath->mimeType();
    }

    /**
     * @return string
     */
    public function extension() {
        return $this->fmPath->extension();
    }

    /**
     * @return string
     */
    public function url() {
        if ($this->isDirectory()) {
            return $this->fmPath->path('working_dir');
        }

        return $this->fmPath->url();
    }

    /**
     * @return string
     */
    public function size() {
        return $this->isFile() ? $this->humanFilesize($this->sizeBytes()) : '';
    }

    /**
     * Raw byte count, used by Path::sortByColumn() to sort the File Size
     * column numerically -- size() above is the human-readable string used
     * for display, which sorts incorrectly as plain text (e.g. "9.5 MB" vs
     * "10.2 MB"). Cached on its own (not just via __get()'s $attributes,
     * which only covers whichever single key was looked up) so size() and
     * sortByColumn() -- called at different points in the request -- always
     * see the exact same number instead of two separate disk calls.
     *
     * @return int
     */
    public function sizeBytes() {
        if (!array_key_exists('size_bytes_raw', $this->attributes)) {
            $this->attributes['size_bytes_raw'] = $this->isFile() ? (int) $this->fmPath->size() : 0;
        }

        return $this->attributes['size_bytes_raw'];
    }

    /**
     * @return false|int
     */
    public function time() {
        if (!$this->isDirectory()) {
            return $this->fmPath->lastModified();
        }

        return false;
    }

    /**
     * @return null|string
     */
    public function thumbUrl() {
        if ($this->isDirectory()) {
            return curl::httpbase() . 'system/media/img/filemanager/folder.png';
        }
        if ($this->isImage()) {
            return $this->fmPath->thumb($this->hasThumb())->url();
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function icon() {
        //return null if not exists
        if ($this->fmPath->exists()) {
            if ($this->isDirectory()) {
                return 'fa-folder-o';
            }
            if ($this->isImage()) {
                return 'fa-image';
            }

            return $this->extension();
        }

        return null;
    }

    /**
     * @return string
     */
    public function type() {
        if ($this->isDirectory()) {
            return c::trans('element/filemanager.type-folder');
        }
        if ($this->isImage()) {
            return $this->mimeType();
        }

        return $this->helper->getFileType($this->extension());
    }

    /**
     * @return bool
     */
    public function hasThumb() {
        if (!$this->isImage()) {
            return false;
        }
        $fmPath = clone $this->fmPath;
        if (!$fmPath->thumb()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function shouldCreateThumb() {
        if (!$this->helper->config('should_create_thumbnails')) {
            return false;
        }
        if (!$this->isImage()) {
            return false;
        }
        if (in_array($this->mimeType(), ['image/gif', 'image/svg+xml'])) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function get() {
        return $this->fmPath->get();
    }

    /**
     * Make file size readable.
     *
     * @param int $bytes    file size in bytes
     * @param int $decimals decimals
     *
     * @return string
     */
    public function humanFilesize($bytes, $decimals = 2) {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = (int) floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), @$size[$factor]);
    }
}
