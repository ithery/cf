<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CResources_Exception_UrlCannotBeDetermined extends CResources_Exception {

    public static function resourceNotPubliclyAvailable($storagePath, $publicPath) {
        return new static("Storage path `{$storagePath}` is not part of public path `{$publicPath}`");
    }

    public static function filesystemDoesNotSupportTemporaryUrls() {
        return new static('Generating temporary URLs only works on the S3 filesystem driver');
    }

}
