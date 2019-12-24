<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CComparator {

    public static function createFactory() {
        return new CComparator_Factory();
    }

    public static function createExporter() {
        return new CComparator_Exporter();
    }

}
