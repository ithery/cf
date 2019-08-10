<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 2:08:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_File_Connector_FileManager_FM {

    public function path() {
        return new CManager_File_Connector_FileManager_FM_Path($this);
    }

    public function input($key) {
        return $this->translateFromUtf8(CHTTP::request()->input($key));
    }

    public function config($key) {
        return CF::config('filemanager.' . $key);
    }

    /**
     * Get current lfm type.
     *
     * @return string
     */
    public function currentFmType() {
        $lfm_type = 'file';
        $request_type = lcfirst(cstr::singular($this->input('type') ?: ''));
        $available_types = array_keys($this->config('folder_categories') ?: []);
        if (in_array($request_type, $available_types)) {
            $lfm_type = $request_type;
        }
        return $lfm_type;
    }

    public function availableMimeTypes() {
        return $this->config('folder_categories.' . $this->currentFmType() . '.valid_mime');
    }

    /**
     * Translate file name to make it compatible on Windows.
     *
     * @param  string  $input  Any string.
     * @return string
     */
    public function translateFromUtf8($input) {
        if ($this->isRunningOnWindows()) {
            $input = iconv('UTF-8', mb_detect_encoding($input), $input);
        }
        return $input;
    }

    /**
     * Check current operating system is Windows or not.
     *
     * @return bool
     */
    public function isRunningOnWindows() {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public function maxUploadSize() {
        return $this->config('folder_categories.' . $this->currentFmType() . '.max_size');
    }

    public function getTranslation() {
        $translator = CTranslation::translator();
        $data = $translator->get('filemanager');
        return $data;
    }

    public function allowFolderType($type) {
        if ($type == 'user') {
            return $this->allowMultiUser();
        } else {
            return $this->allowShareFolder();
        }
    }

    /**
     * Check if users are allowed to use their private folders.
     *
     * @return bool
     */
    public function allowMultiUser() {
        return $this->config('allow_multi_user') === true;
    }

    /**
     * Check if users are allowed to use the shared folder.
     * This can be disabled only when allowMultiUser() is true.
     *
     * @return bool
     */
    public function allowShareFolder() {
        if (!$this->allowMultiUser()) {
            return true;
        }
        return $this->config('allow_share_folder') === true;
    }

    public function getDisplayMode() {
        $type_key = $this->currentFmType();
        $startup_view = $this->config('folder_categories.' . $type_key . '.startup_view');
        $view_type = 'grid';
        $target_display_type = $this->input('show_list') ?: $startup_view;
        if (in_array($target_display_type, ['list', 'grid'])) {
            $view_type = $target_display_type;
        }
        return $view_type;
    }

    public function getStorage($storage_path) {
        return new CManager_File_Connector_FileManager_FM_StorageRepository($storage_path, $this);
    }

    public function getCategoryName() {
        $type = $this->currentFmType();
        return $this->config('folder_categories.' . $type . '.folder_name', 'files');
    }

    public function getRootFolder($type = null) {
        if (is_null($type)) {
            $type = 'share';
            if ($this->allowFolderType('user')) {
                $type = 'user';
            }
        }
        if ($type === 'user') {
            $folder = $this->getUserSlug();
        } else {
            $folder = $this->config('shared_folder_name');
        }
        // the slash is for url, dont replace it with directory seperator
        $appCode = CF::appCode();
        $orgCode = CF::orgCode();
        $path = '/';
        if (strlen($appCode) > 0) {
            $path .= $appCode . '/';
        }
        if (strlen($orgCode) > 0) {
            $path .= $orgCode . '/';
        }
        return $path . $folder;
    }

    public function getUserSlug() {
        $config = $this->config('user_folder_name');
        if (is_callable($config)) {
            return call_user_func($config);
        }
        if (class_exists($config)) {
            return app()->make($config)->userField();
        }
        $app = CApp::instance();
        $user = $app->user();
        return $user ? $user->username : '';
    }

    /**
     * Get directory seperator of current operating system.
     *
     * @return string
     */
    public function ds() {
        $ds = DS;
        if ($this->isRunningOnWindows()) {
            $ds = '\\';
        }
        return $ds;
    }

    /**
     * Shorter function of getting localized error message..
     *
     * @param  mixed  $error_type  Key of message in lang file.
     * @param  mixed  $variables   Variables the message needs.
     * @return string
     */
    public function error($error_type, $variables = []) {
        throw new \Exception(clang::__('filemanager.error-' . $error_type, $variables));
    }

    /**
     * Get only the file name.
     *
     * @param  string  $path  Real path of a file.
     * @return string
     */
    public function getNameFromPath($path) {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    public function getThumbFolderName() {
        return $this->config('thumb_folder_name');
    }

    public function getFileIcon($ext) {
        return $this->config("file_icon_array.{$ext}", 'fa-file-o');
    }

    public function getFileType($ext) {
        return $this->config("file_type_array.{$ext}", 'File');
    }

}
