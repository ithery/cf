<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 2:08:47 AM
 */
class CManager_File_Connector_FileManager_FM {
    const PACKAGE_NAME = 'capp-filemanager';

    const DS = '/';

    protected $config = [];

    protected $labels;

    public function __construct($config = []) {
        $this->config = $config;

        $this->dispatch(new CManager_File_Connector_FileManager_Event_ManagerInitialized($this));
    }

    /**
     * Dispatch an event and call the listeners.
     *
     * //@param string|object $event
     * //@param mixed         $payload
     * //@param bool          $halt
     *
     * @return null|array
     */
    public static function dispatch() {
        $args = func_get_args();
        $event = carr::get($args, 0);
        $payload = array_slice($args, 1);

        return CEvent::dispatcher()->dispatch($event, $payload);
    }

    public function path() {
        return new CManager_File_Connector_FileManager_FM_Path($this);
    }

    /**
     * Get Input.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function input($key) {
        return $this->translateFromUtf8(CHTTP::request()->input($key));
    }

    public function config($key, $default = null) {
        return carr::get($this->config, $key, CF::config('filemanager.' . $key, $default));
    }

    public function configData() {
        return $this->config;
    }

    /**
     * Get current lfm type.
     *
     * @return string
     */
    public function currentFmType() {
        $fmType = 'file';
        $request_type = lcfirst(cstr::singular($this->input('type') ?: ''));
        $available_types = array_keys($this->config('folder_categories') ?: []);
        if (in_array($request_type, $available_types)) {
            $fmType = $request_type;
        }

        return $fmType;
    }

    public function availableMimeTypes() {
        return $this->config('folder_categories.' . $this->currentFmType() . '.valid_mime');
    }

    /**
     * Translate file name to make it compatible on Windows.
     *
     * @param string $input any string
     *
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
        if ($this->labels == null) {
            $translator = CTranslation::translator();
            $this->labels = $translator->getLoader()->load($translator->getLocale(), 'element/filemanager');
        }

        return $this->labels;
    }

    public function getLabel($key, $default = null) {
        return carr::get($this->getTranslation(), $key, $default);
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
        $typeKey = $this->currentFmType();
        $startupView = $this->config('folder_categories.' . $typeKey . '.startup_view');
        $viewType = 'grid';
        $targetDisplayType = $this->input('showList') ?: $startupView;
        if (in_array($targetDisplayType, ['list', 'grid'])) {
            $viewType = $targetDisplayType;
        }

        return $viewType;
    }

    public function getStorage($storagePath) {
        return new CManager_File_Connector_FileManager_FM_StorageRepository($storagePath, $this);
    }

    public function getCategoryName() {
        $type = $this->currentFmType();

        $categoryName = $this->config('folder_categories.' . $type . '.folder_name', 'files');
        $rootPath = ltrim($this->config('root_path'), '/');
        if (strlen($rootPath) > 0) {
            $rootPath = rtrim($rootPath) . '/' . rtrim($categoryName, '/');
        }

        return $rootPath;
    }

    public function getRootFolder($type = null) {
        return '/';
    }

    public function getUserSlug() {
        $config = $this->config('user_folder_name');
        if (is_callable($config)) {
            return call_user_func($config);
        }
        if (class_exists($config)) {
            // return app()->make($config)->userField();
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
     * @param string $errorType key of message in lang file
     * @param array  $variables variables the message needs
     *
     * @return string
     */
    public function error($errorType, array $variables = []) {
        throw new \Exception(clang::__('filemanager.error-' . $errorType, $variables));
    }

    /**
     * Get only the file name.
     *
     * @param string $path real path of a file
     *
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

    public function connectorUrl() {
        return $this->config('connector_url', curl::base() . 'cresenity/connector/fm');
    }
}
