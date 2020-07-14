<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CAjax_Trait_DataTableTrait {

    public function getTable() {
        $data = $this->ajaxMethod->getData();
        $table = unserialize(carr::get($data, 'table'));
    }

}
