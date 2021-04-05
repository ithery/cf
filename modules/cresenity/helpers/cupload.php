<?php

defined('SYSPATH') or die('No direct access allowed.');

//@codingStandardsIgnoreStart
class cupload {
    /**
     * Save an uploaded file to a new location.
     *
     * @param mixed  $file      name of $_FILE input or array of upload data
     * @param string $filename  new filename
     * @param string $directory new directory
     * @param int    $chmod     chmod mask
     *
     * @return string full path to new file
     */
    public static function save($file, $filename = null, $directory = null, $chmod = 0644) {
        // Load file data from FILES if not passed as array
        $file = is_array($file) ? $file : $_FILES[$file];

        if ($filename === null) {
            // Use the default filename, with a timestamp pre-pended
            $filename = time() . $file['name'];
        }

        if (CF::config('upload.remove_spaces') === true) {
            // Remove spaces from the filename
            $filename = preg_replace('/\s+/', '_', $filename);
        }

        if ($directory === null) {
            // Use the pre-configured upload directory
            $directory = CF::config('upload.directory', true);
        }

        // Make sure the directory ends with a slash
        $directory = rtrim($directory, '/') . '/';

        if (!is_dir($directory) and CF::config('upload.create_directories') === true) {
            // Create the upload directory
            mkdir($directory, 0777, true);
        }

        if (!is_writable($directory)) {
            // throw new CF_Exception('upload.not_writable', $directory);
            throw new CF_Exception(CF::lang('upload.not_writable'));
        }

        if (is_uploaded_file($file['tmp_name']) and move_uploaded_file($file['tmp_name'], $filename = $directory . $filename)) {
            if ($chmod !== false) {
                // Set permissions on filename
                chmod($filename, $chmod);
            }

            // Return new file path
            return $filename;
        }

        return false;
    }

    public static function save_array($file, $filename = null, $directory = null, $chmod = 0644) {
        // Load file data from FILES if not passed as array
        $file = is_array($file) ? $file : $_FILES[$file];
        $file_rotate = carr::rotate($file);

        foreach ($file_rotate as $file) {
            if ($filename === null) {
                // Use the default filename, with a timestamp pre-pended
                $filename = time() . $file['name'];
            }

            if (CF::config('upload.remove_spaces') === true) {
                // Remove spaces from the filename
                $filename = preg_replace('/\s+/', '_', $filename);
            }

            if ($directory === null) {
                // Use the pre-configured upload directory
                $directory = CF::config('upload.directory', true);
            }

            // Make sure the directory ends with a slash
            $directory = rtrim($directory, '/') . '/';

            if (!is_dir($directory) and CF::config('upload.create_directories') === true) {
                // Create the upload directory
                mkdir($directory, 0777, true);
            }

            if (!is_writable($directory)) {
                // throw new CF_Exception('upload.not_writable', $directory);
                throw new Exception(CF::lang('upload.not_writable'));
            }

            if (is_uploaded_file($file['tmp_name']) and move_uploaded_file($file['tmp_name'], $filename = $directory . $filename)) {
                if ($chmod !== false) {
                    // Set permissions on filename
                    chmod($filename, $chmod);
                }

                // Return new file path
                return $filename;
            }
        }

        return false;
    }

    /* Validation Rules */

    /**
     * Tests if input data is valid file type, even if no upload is present.
     *
     * @param array $_FILES item
     * @param mixed $file
     *
     * @return bool
     */
    public static function valid($file) {
        return (is_array($file)
                and isset($file['error'])
                and isset($file['name'])
                and isset($file['type'])
                and isset($file['tmp_name'])
                and isset($file['size']));
    }

    /**
     * Tests if input data has valid upload data.
     *
     * @param array $_FILES item
     *
     * @return bool
     */
    public static function required(array $file) {
        return (isset($file['tmp_name'])
                and isset($file['error'])
                and is_uploaded_file($file['tmp_name'])
                and (int) $file['error'] === UPLOAD_ERR_OK);
    }

    /**
     * Validation rule to test if an uploaded file is allowed by extension.
     *
     * @param array $_FILES item
     * @param   array    allowed file extensions
     *
     * @return bool
     */
    public static function type(array $file, array $allowed_types) {
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            return true;
        }

        // Get the default extension of the file
        $extension = strtolower(substr(strrchr($file['name'], '.'), 1));

        // Get the mime types for the extension
        $mime_types = CF::config('mimes.' . $extension);

        // Make sure there is an extension, that the extension is allowed, and that mime types exist
        return (!empty($extension) and in_array($extension, $allowed_types) and is_array($mime_types));
    }

    /**
     * Validation rule to test if an uploaded file is allowed by file size.
     * File sizes are defined as: SB, where S is the size (1, 15, 300, etc) and
     * B is the byte modifier: (B)ytes, (K)ilobytes, (M)egabytes, (G)igabytes.
     * Eg: to limit the size to 1MB or less, you would use "1M".
     *
     * @param array $_FILES item
     * @param   array    maximum file size
     *
     * @return bool
     */
    public static function size(array $file, array $size) {
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            return true;
        }

        // Only one size is allowed
        $size = strtoupper($size[0]);

        if (!preg_match('/[0-9]++[BKMG]/', $size)) {
            return false;
        }

        // Make the size into a power of 1024
        switch (substr($size, -1)) {
            case 'G': $size = intval($size) * pow(1024, 3);
                break;
            case 'M': $size = intval($size) * pow(1024, 2);
                break;
            case 'K': $size = intval($size) * pow(1024, 1);
                break;
            default: $size = intval($size);
                break;
        }

        // Test that the file is under or equal to the max size
        return ($file['size'] <= $size);
    }

    /* upload logic */

    public static function create_upload_folder($type, $id) {
        $upload_directory = DOCROOT . 'upload' . '/';
        $org = CApp::instance()->org();
        $org_path = '';
        if ($org != null) {
            $org_path = $org->org_code . '/';
        }
        $org_directory = $upload_directory . $org_path;
        ctemp::makedir($org_directory);
        $type = explode('.', $type);
        $type_directory = $org_directory;
        foreach ($type as $t) {
            if (strlen(trim($t)) > 0) {
                $type_directory = $type_directory . $t . '/';
                ctemp::makedir($type_directory);
            }
        }

        $id_directory = $type_directory . $id . '/';

        ctemp::makedir($id_directory);
    }

    public static function delete_all_file($type, $id, $filename) {
        $file = cupload::get_upload_path($type, $id) . $filename;
        if (is_file($file)) {
            unlink($file);
        }
    }

    public static function get_upload_path($type, $id) {
        $upload_directory = DOCROOT . 'upload' . '/';
        $org = CApp::instance()->org();
        $org_path = '';
        if ($org != null) {
            $org_path = $org->org_code . '/';
        }
        $org_directory = $upload_directory . $org_path;

        $type = explode('.', $type);
        $type_directory = $org_directory;
        foreach ($type as $t) {
            if (strlen(trim($t)) > 0) {
                $type_directory = $type_directory . $t . '/';
            }
        }
        $id_directory = $type_directory . $id . '/';
        //die("cupload#210 ".$id_directory);
        return $id_directory;
    }

    public static function get_upload_src($type, $id, $filename) {
        $org = CApp::instance()->org();
        //$upload_directory = DOCROOT . 'upload' . "/";
        //$upload_directory = CF::get_dir('upload');
        $upload_directory = curl::base() . 'upload/';
        //$org_folder = "";
        /* if ($org != null) {
          $org_folder = $org->org_code . "/";
          }
          $org_directory = $upload_directory . $org_folder; */

        $org_path = '';
        if ($org != null) {
            $org_path = $org->org_code . '/';
        }
        $org_directory = $upload_directory . $org_path;

        //$upload_directory = curl::base().'upload'.'/';
        $type = explode('.', $type);
        $type_directory = $org_directory;
        foreach ($type as $t) {
            if (strlen(trim($t)) > 0) {
                $type_directory = $type_directory . $t . '/';
            }
        }
        $id_directory = $type_directory . $id . '/';
        //die("cupload#239 ".$id_directory);
        return $id_directory . $filename;
    }
}
