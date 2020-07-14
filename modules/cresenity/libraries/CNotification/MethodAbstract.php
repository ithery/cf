<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CNotification_MethodAbstract implements CNotification_MethodInterface {

    use CTrait_HasOptions;
    
    
    public function onNotificatonSent($logNotificationModel) {
        
    }
   
}
