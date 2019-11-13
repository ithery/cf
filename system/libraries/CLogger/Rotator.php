<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CLogger_Rotator {

    /**
     * 
     * @param string $path
     * @return \CLogger_Rotate
     */
    public static function createRotate($path) {
        $rotate = new CLogger_Rotator_Rotate($path);
        return $rotate;
        
    }

}
