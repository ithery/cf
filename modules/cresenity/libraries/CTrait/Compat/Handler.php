<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CTrait_Compat_Handler {

    public function set_url_param($param) {
        return $this->setUrlParam($param);
    }

}
