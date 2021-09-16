<?php

defined('SYSPATH') or die('No direct access allowed.');

 /**
  * @author Hery Kurniawan
  * @license Ittron Global Teknologi <ittron.co.id>
  *
  * @since Feb 16, 2018, 6:51:44 AM
  */
 //@codingStandardsIgnoreStart
 /**
  * @see CObservable_Listener
  */
 trait CTrait_Compat_Listener {
     public function set_confirm($bool) {
         return $this->setConfirm($bool);
     }

     public function set_no_double($bool) {
         return $this->setNoDouble($bool);
     }

     public function set_confirm_message($message) {
         return $this->setConfirmMessage($message);
     }

     public function set_owner($owner) {
         return $this->setOwner($owner);
     }

     public function set_handler_url_param($param) {
         return $this->setHandlerUrlParam($param);
     }

     /**
      * @param string $handlerName
      *
      * @return CObservable_Listener_Handler
      *
      * @deprecated
      */
     public function add_handler($handlerName) {
         return $this->addHandler($handlerName);
     }
 }
