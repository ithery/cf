<?php

class CTemporary_Helper {
    /**
     * @param string $path
     *
     * @return string
     */
    public static function sanitizePath($path) {
        $path = rtrim($path);

        return rtrim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $name
     *
     * @throws CTemporary_Exception_InvalidDirectoryName
     *
     * @return string
     */
    protected function sanitizeName($name) {
        if (!$this->isValidDirectoryName($name)) {
            throw CTemporary_Exception_InvalidDirectoryName::create($name);
        }

        return trim($name);
    }

    /**
     * @param string $directoryName
     *
     * @return bool
     */
    public static function isValidDirectoryName($directoryName) {
        return strpbrk($directoryName, '\\/?%*:|"<>') === false;
    }
}
