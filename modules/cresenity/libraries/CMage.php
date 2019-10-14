<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CMage {

    public static function createOption() {
        return CMage_Factory::createOption();
    }

    /**
     * 
     * @param CMage_Option $option
     * @return \CMage_Caster
     */
    public static function createCaster($mage, $controller) {
        return CMage_Factory::createCaster($mage, $controller);
    }

    public static function request() {
        return new CMage_Request();
    }

}
