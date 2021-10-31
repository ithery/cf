<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CMage_Mage_Trait_AuthorizableTrait {

    public $indexPermission = null;
    public $addPermission = null;
    public $editPermission = null;
    public $deletePermission = null;

    /**
     * Determine if the current user can create new resources.
     *
     * @return bool
     */
    public function authorizeToAdd() {
        if (strlen($this->addPermission) > 0) {
            if (!CApp_Base::havePermission($this->addPermission)) {
                return false;
            }
        }

        return true;
    }

}
