<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 5:32:56 AM
 */
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CManager_File_Connector_FileManager_FM_Item {
    public $attributes = [];

    /**
     * @var CManager_File_Connector_FileManager_FM_Path
     */
    private $fmPath;

    private $helper;

    private $columns = ['name', 'url', 'time', 'icon', 'is_file', 'is_image', 'thumb_url'];

    public function __construct(CManager_File_Connector_FileManager_FM_Path $fmPath, CManager_File_Connector_FileManager_FM $helper) {
        $this->fmPath = $fmPath->thumb(false);
        $this->helper = $helper;
    }

    public function __get($var_name) {
        if (!array_key_exists($var_name, $this->attributes)) {
            $function_name = cstr::camel($var_name);
            $this->attributes[$var_name] = $this->$function_name();
        }

        return $this->attributes[$var_name];
    }

    public function fill() {
        foreach ($this->columns as $column) {
            $this->__get($column);
        }

        return $this;
    }

    public function name() {
        return $this->fmPath->getName();
    }

    public function path($type = 'absolute') {
        return $this->fmPath->path($type);
    }

    public function isDirectory() {
        return $this->fmPath->isDirectory();
    }

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
     * Get mime type of a file.
     *
     * @return string
     */
    public function mimeType() {
        // TODO: uploaded file
        // if ($file instanceof UploadedFile) {
        //     return $file->getMimeType();
        // }
        return $this->fmPath->mimeType();
    }

    public function extension() {
        return $this->fmPath->extension();
    }

    public function url() {
        if ($this->isDirectory()) {
            return $this->fmPath->path('working_dir');
        }

        return $this->fmPath->url();
    }

    public function size() {
        return $this->isFile() ? $this->humanFilesize($this->fmPath->size()) : '';
    }

    public function time() {
        if (!$this->isDirectory()) {
            return $this->fmPath->lastModified();
        }

        return false;
    }

    public function thumbUrl() {
        if ($this->isDirectory()) {
            return curl::httpbase() . 'system/media/img/filemanager/folder.png';
        }
        if ($this->isImage()) {
            return $this->fmPath->thumb($this->hasThumb())->url(true);
        }

        return null;
    }

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

    public function type() {
        if ($this->isDirectory()) {
            return c::trans('filemanager.type-folder');
        }
        if ($this->isImage()) {
            return $this->mimeType();
        }

        return $this->helper->getFileType($this->extension());
    }

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
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), @$size[$factor]);
    }
}
