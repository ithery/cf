<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CVendor {

    public static function digitalOcean($accessToken) {
        return new CVendor_DigitalOcean($accessToken);
    }

}
