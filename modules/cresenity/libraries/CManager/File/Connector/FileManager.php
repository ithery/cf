<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 28, 2019, 1:50:58 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_File_Connector_FileManager extends CManager_File_ConnectorAbstract {

    private static $docroot = NULL;

    public static function SetDocRoot($path) {
        self::$docroot = $path;
    }

    private static $relroot = '/';

    public static function SetRelRoot($path) {
        self::$relroot = $path;
    }

    private static $data = array();

    static function GetRelativePath($path) {
        $docroot = self::$docroot ? self::$docroot : $_SERVER['DOCUMENT_ROOT'];
        $path = realpath($path);
        $path = str_replace($docroot, self::$relroot, $path);
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        return $path;
    }

    static function GetPathFolder() {
        return self::GetRelativePath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
    }

    static function GetPathSelf() {
        return self::GetRelativePath(__FILE__);
    }

    static function GetPathJS() {
        return self::GetPathFolder() . 'jquery.fileManager.js';
    }

    static function GetPathCSS() {
        return self::GetPathFolder() . 'jquery.fileManager.css';
    }

    static function ResolvePath($path) {
        $newpath = preg_replace('/[^\/]+\/\.\.\/?/', '', $path);
        if ($newpath != $path)
            $newpath = self::ResolvePath($newpath);
        return $newpath;
    }

    static function AddIcon($path, $title = '', $folder = false, $icon = '') {
        self::$data[] = array('path' => $path, 'title' => $title, 'type' => $folder, 'icon' => $icon);
    }

    public static function run($rootPath, $deleteCallback = null, $renameCallback = null, $iconCallback = null) {
        $pMod = array_key_exists('path', $_POST) ? $_POST['path'] : '';
        $path = $rootPath . '/' . trim($pMod, '/');
        $path = self::ResolvePath($path);
        $path = rtrim($path, '/');
        if (strpos($path, $rootPath) === FALSE)
            $path = $rootPath;

        if (!file_exists($path))
            mkdir($path, octdec('0777'), true);

        if (isset($_FILES['file']))
            return self::ProcessUpload($path);

        if (isset($_POST['delete'])) {
            $from = self::ResolvePath($path . '/' . $_POST['delete']);
            if (strpos($from, $rootPath) === FALSE) {
                echo 'alert("Can only perform operations within the root path");';
                return false;
            }
            if (!file_exists($from)) {
                echo 'alert("File or Folder no longer exists");';
                return false;
            }
            try {
                if (is_dir($from))
                    rmdir($from);
                else
                    unlink($from);
            } catch (Exception $e) {
                echo 'alert("Cannot Delete.  Folder may not be empty.");';
                return false;
            }
            return true;
        }
        if (array_key_exists('mFrom', $_POST) && array_key_exists('mTo', $_POST)) {
            $from = self::ResolvePath($path . '/' . $_POST['mFrom']);
            $to = self::ResolvePath($path . '/' . $_POST['mTo']);
            if (strpos($from, $rootPath) === FALSE || strpos($to, $rootPath) === FALSE) {
                echo 'alert("Can only perform operations within the root path");';
                return false;
            }
            if (file_exists($to)) {
                echo 'alert("Destination already exists");';
                return false;
            }
            try {
                rename($from, $to);
            } catch (Exception $e) {
                echo $e->getMessage();
                echo 'alert("Cannot move or rename.");';
                return false;
            }
            if (is_callable($renameCallback))
                call_user_func($renameCallback, $from, $to);
            return true;
        }

        $glob = glob($path . '/{,.}*', GLOB_BRACE);
        natcasesort($glob);
        $files = array_merge(array_filter($glob, 'is_dir'), array_filter($glob, 'is_file'));
        foreach ($files as $file) {
            $filename = basename($file);
            if ($filename === '..' || $filename === '.')
                continue;
            if (!is_dir($file) && array_key_exists('filter', $_POST) && !preg_match('/' . $_POST['filter'] . '/i', $filename))
                continue;
            $icon = (is_callable($iconCallback)) ? call_user_func($iconCallback, $file) : '';
            self::AddIcon($filename, $filename, is_dir($file) ? 1 : 0, $icon);
        }

        // uPath is full path less rootpath less filename
        $uPath = substr(self::GetRelativePath($path), strlen(self::GetRelativePath($rootPath)));
        if (!$uPath)
            $uPath = '';
        header('Content-Type: application/json');
        echo json_encode(array('rootPath' => self::GetRelativePath($rootPath), 'path' => $uPath, 'files' => self::$data));
    }

    public static function ProcessUpload($path) {
        if (ob_get_level())
            ob_end_clean();
        $destination = realpath($path);

        // HTTP headers for no cache etc
        header('Content-type: application/json; charset=UTF-8');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // Settings
        $targetDir = $destination; // ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $cleanupTargetDir = false; // Remove old files
        $maxFileAge = 60 * 60; // Temp file age in seconds
        // 5 minutes execution time
        //set_time_limit(0);
        // usleep(5000);
        // Get parameters
        $chunk = array_key_exists('chunk', $_REQUEST) ? $_REQUEST["chunk"] : 0;
        $chunks = array_key_exists('chunks', $_REQUEST) ? $_REQUEST["chunks"] : 0;
        $fileName = array_key_exists('name', $_REQUEST) ? $_REQUEST["name"] : '';

        // Clean the fileName for security reasons
        $fileName = preg_replace('/[^\w\._]+/', '', $fileName);
        if (is_dir($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
            echo '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}';
        }

        // Create target dir
        if (!file_exists($targetDir)) {
            mkdir($targetDir);
            chmod($targetDir, 0777);
        }

        // Remove old temp files
        if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
            while (($file = readdir($dir)) !== false) {
                $filePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // Remove temp files if they are older than the max age
                if (preg_match('/\\.tmp$/', $file) && (filemtime($filePath) < time() - $maxFileAge))
                    unlink($filePath);
            }

            closedir($dir);
        } else {
            echo '{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}';
        }

        $contentType = '';
        // Look for the content type header
        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

        if (isset($_SERVER["CONTENT_TYPE"]))
            $contentType = $_SERVER["CONTENT_TYPE"];

        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                // Open temp file
                $out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
                if ($out) {
                    // Read binary input stream and append it to temp file
                    $in = fopen($_FILES['file']['tmp_name'], "rb");

                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);
                    } else {
                        echo '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}';
                    }

                    fclose($out);
                    unlink($_FILES['file']['tmp_name']);
                } else {
                    echo '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}';
                }
            } else {
                echo '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}';
            }
        } else {
            // Open temp file
            $out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = fopen("php://input", "rb");

                if ($in) {
                    while ($buff = fread($in, 4096))
                        fwrite($out, $buff);
                } else {
                    echo '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}';
                }

                fclose($out);
            } else {
                echo '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}';
            }
        }

        // Return JSON-RPC response
        echo '{"jsonrpc" : "2.0", "result" : null, "id" : "id"}';
    }

}
