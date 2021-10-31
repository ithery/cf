<?php

 //@codingStandardsIgnoreStart
 trait CTrait_Compat_Handler {
     public function set_url_param($param) {
         return $this->setUrlParam($param);
     }
 }
