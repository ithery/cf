<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CResources_Exception_InvalidUrlGenerator extends CResources_Exception {

    public static function doesntExist($class) {
        return new static("Class {$class} doesn't exist");
    }

    public static function isntAUrlGenerator($class) {
        return new static("Class {$class} must implement `Spatie\\MediaLibrary\\UrlGenerator\\UrlGenerator`");
    }

}
