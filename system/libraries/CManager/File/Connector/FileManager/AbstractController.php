<?php

use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_AbstractController {
    /**
     * @var CManager_File_Connector_FileManager
     */
    protected $fileManager;

    /**
     * @var CManager_File_Connector_FileManager_FM
     */
    protected $fm;

    public function __construct(CManager_File_Connector_FileManager $fileManager) {
        $this->fileManager = $fileManager;
        $app = CApp::instance();
        $app->setLoginRequired(false);
        $filemanagerTheme = $this->fm()->config('theme', 'null');
        CManager::theme()->setThemeCallback(function ($theme) use ($filemanagerTheme) {
            return $filemanagerTheme;
        });
    }

    /**
     * @return CManager_File_Connector_FileManager_FM
     */
    protected function fm() {
        if ($this->fm == null) {
            $this->fm = new FM($this->fileManager->getConfig());
        }

        return $this->fm;
    }

    public function error($error_type, $variables = []) {
        return $this->fm()->error($error_type, $variables);
    }

    public function getDisk() {
        return CStorage::instance()->disk($this->fm()->config('disk'));
    }

    /**
     * Standard Cresenity ajax response shape (matches CAjax_Engine::toJsonResponse()),
     * used across all FileManager ajax actions instead of the old bare 'OK' string /
     * raw array-of-errors responses.
     *
     * @param array $data
     *
     * @return \CHTTP_JsonResponse
     */
    protected function successResponse($data = []) {
        return c::response()->json([
            'errCode' => 0,
            'errMessage' => '',
            'data' => $data,
        ]);
    }

    /**
     * @param string $message
     * @param array  $data
     *
     * @return \CHTTP_JsonResponse
     */
    protected function errorResponse($message, $data = []) {
        return c::response()->json([
            'errCode' => 1,
            'errMessage' => $message,
            'data' => $data,
        ]);
    }
}
