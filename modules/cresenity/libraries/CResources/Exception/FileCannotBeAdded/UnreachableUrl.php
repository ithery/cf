<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CResources_Exception_FileCannotBeAdded_UnreachableUrl extends CResources_Exception_FileCannotBeAdded {

    public static function create($url) {
        return new static("Url `{$url}` cannot be reached");
    }

}
