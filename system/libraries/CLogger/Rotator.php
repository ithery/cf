<?php

class CLogger_Rotator {
    /**
     * @param string $path
     *
     * @return \CLogger_Rotator_Rotate
     */
    public static function createRotate($path) {
        $rotate = new CLogger_Rotator_Rotate($path);

        return $rotate;
    }
}
