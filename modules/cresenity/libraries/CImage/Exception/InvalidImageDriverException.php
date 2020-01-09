<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CImage_Exception_InvalidImageDriverException extends Exception {

    public static function driver($driver) {
        return new self("Driver must be `gd` or `imagick`. `{$driver}` provided.");
    }

}
