<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class CMage_Factory {
    /**
     * 
     * @return \CMage_Option
     */
    public static function createOption() {
        return new CMage_Option();
    }
}