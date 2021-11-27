<?php

class CTemporary_PathGenerator_DefaultPathGenerator {
    public function getPath($extension = null) {
        if (!$extension) {
            $extension = 'temp';
        }

        if (cstr::startsWith($extension, '.')) {
            $extension = '.' . $extension;
        }

        $filename = date('Ymd') . cutils::randmd5() . (strlen($extension) > 0 ? $extension : '');

        $depth = 5;
        $mainFolder = substr($filename, 0, 8);
        $path = '';
        $path = $path . $mainFolder . DIRECTORY_SEPARATOR;

        $basefile = basename($filename);
        for ($i = 0; $i < $depth; $i++) {
            $c = '_';
            if (strlen($basefile) > ($i + 1)) {
                $c = substr($basefile, $i + 8, 1);
                if (strlen($c) == 0) {
                    $c = '_';
                }
                $path = $path = $path . $c . DIRECTORY_SEPARATOR;
            }
        }

        return $path . $filename;
    }
}
