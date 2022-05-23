<?php
/**
 * @see CConfig
 */
class CConfig_Loader {
    public static function load($configName) {
        $items = [];
        $files = CF::findFile('config', $configName, $required = false, $ext = false, $refresh = true);

        //add backward compatibility
        //TODO: remove folder config in DOCROOT
        if (!is_array($files)) {
            $files = [];
        }
        if (file_exists(DOCROOT . 'config' . DS . $configName . EXT)) {
            $files[] = DOCROOT . 'config' . DS . $configName . EXT;
        }

        foreach ($files as $file) {
            $cfg = include $file;
            if (!is_array($cfg) && isset($config)) {
                //backward compatibility with older config
                $cfg = $config;
                unset($config);
            }
            if (!is_array($cfg)) {
                //there is an invalid format
                throw new CException('Invalid config format in :file', [':file' => $file]);
            }
            $items = carr::merge($items, $cfg);
        }

        return $items;
    }

    public static function data($configName) {
        $files = CF::findFile('config', $configName);

        //add backward compatibility
        //TODO: remove folder config in DOCROOT
        if (!is_array($files)) {
            $files = [];
        }
        if (file_exists(DOCROOT . 'config' . DS . $configName . EXT)) {
            $files[] = DOCROOT . 'config' . DS . $configName . EXT;
        }

        //reverse ordering to set priority
        if ($files == null) {
            //var_dump(debug_backtrace());
            //throw new CF_Exception('file config '.$group.' not found');
            $files = [];
        }

        $resultFiles = [];
        $resultData = [];
        foreach ($files as $file) {
            $cfg = include $file;
            if (!is_array($cfg)) {
                //backward compatibility with older config
                if (isset($config)) {
                    $cfg = $config;
                }
            }
            if (!is_array($cfg)) {
                //there is an invalid format
                throw new CException('Invalid config format in :file', [':file' => $file]);
            }
            $cfgFiles = $cfg;
            foreach ($cfgFiles as $key => $val) {
                $cfgFiles[$key] = $file;
            }
            $resultFiles = carr::merge($resultFiles, $cfgFiles);
            $resultData = carr::merge($resultData, $cfg);
        }

        //we will flatten the array of result Data
        $result = [];
        $addToResult = function ($key, $value, &$result) use ($resultFiles, $files) {
            $keyParts = explode('.', $key);
            $resultData = [];
            $resultData['key'] = $key;
            $resultData['value'] = $value;
            $resultData['type'] = gettype($value);
            $file = carr::get($resultFiles, carr::first($keyParts));
            $resultData['file'] = $file;
            $parser = new CConfig_Parser();
            foreach ($files as $fileToParse) {
                $comment = $parser->getComment($fileToParse, $key);
                if (strlen($comment) > 0) {
                    break;
                }
            }
            $resultData['comment'] = $comment;
            $result[] = $resultData;
        };
        $flatten = function ($array, $keyPath = '') use (&$flatten, $addToResult, &$result) {
            foreach ($array as $key => $value) {
                $keyDotted = strlen($keyPath) == 0 ? $key : $keyPath . '.' . $key;
                if (is_array($value)) {
                    if (carr::isAssoc($value)) {
                        //need to be flatten
                        $flatten($value, $keyDotted);
                    } else {
                        $addToResult($keyDotted, $value, $result);
                    }
                } else {
                    $addToResult($keyDotted, $value, $result);
                }
            }
        };
        $flatten($resultData);

        return $result;
    }
}
