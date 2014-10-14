<?php

class clang {

    private static $lang = null;
    private static $langs = array(
        "en" => "English",
        "id" => "Indonesia",
    );

    public static function __($word) {
        if(!is_string($word)) return $word;
		if (clang::$lang == null) {
            //get $lang variable 
            $lang = clang::getlang();
            $files = CF::get_files('lang',$lang);
			
			foreach ($files as $file) {
				$lang = include $file;
				// Merge in configuration
				if (!empty($lang) AND is_array($lang)) {
					foreach ($lang as $k => $v) {
						self::$lang[$k] = $v;
					}
				}
			}
	   
        }
		
        if (isset(clang::$lang[$word])) {
            return clang::$lang[$word];
        }
        return $word;
    }

    public static function current_lang_name() {
        $code = clang::getlang();
        $name = clang::get_lang_name_by_code($code);


        return $name;
    }

    public static function get_lang_name_by_code($code) {
        foreach (self::$langs as $k => $v) {
            if ($k == $code) {
                return $v;
            }
        }
        return null;
    }

    public static function get_lang_list() {

        return self::$langs;
    }

    public static function defaultlang() {
        return ccfg::get('lang');
    }

    public static function getlang() {
        $session = Session::instance();
        $lang = $session->get("lang");
        if ($lang == null)
            $lang = clang::defaultlang();

        return $lang;
    }

    public static function setlang($lang) {
        $session = Session::instance();
        $session->set("lang", $lang);
    }

    public static function langfiles($directory, $filename, $required = FALSE, $ext = FALSE) {
        // NOTE: This test MUST be not be a strict comparison (===), or empty
        // extensions will be allowed!
        if ($ext == '') {
            // Use the default extension
            $ext = EXT;
        } else {
            // Add a period before the extension
            $ext = '.' . $ext;
        }

        // Search path
        $search = $directory . '/' . $filename . $ext;


        // Load include paths
        //$paths = self::$include_paths;
        // Add APPPATH as the first path
        $paths = array(APPPATH);

        foreach (CF::config('core.modules') as $path) {
            if ($path = str_replace('\\', '/', realpath($path))) {
                // Add a valid path
                $paths[] = $path . '/';
            }
        }

        // Add SYSPATH as the last path
        $paths[] = SYSPATH;

        // Nothing found, yet
        $found = NULL;

        if ($directory === 'config' OR $directory === 'i18n' OR $directory === 'lang') {
            // Search in reverse, for merging
            $paths = array_reverse($paths);

            foreach ($paths as $path) {
                if (is_file($path . $search)) {
                    // A matching file has been found
                    $found[] = $path . $search;
                }
            }
        } else {
            foreach ($paths as $path) {
                if (is_file($path . $search)) {
                    // A matching file has been found
                    $found = $path . $search;

                    // Stop searching
                    break;
                }
            }
        }

        if ($found === NULL) {
            if ($required === TRUE) {
                // Directory i18n key
                $directory = 'core.' . inflector::singular($directory);

                // If the file is required, throw an exception
                throw new Kohana_Exception('core.resource_not_found', self::lang($directory), $filename);
            } else {
                // Nothing was found, return FALSE
                $found = FALSE;
            }
        }



        return $found;
    }

}
