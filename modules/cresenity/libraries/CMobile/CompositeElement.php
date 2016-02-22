<?php

    abstract class CMobile_CompositeElement extends CMobile_Element {

     

        public static function factory($id = "", $tag = "div") {
            return new CMobile_CompositeElement($id, $tag);
        }

       

    }

?>