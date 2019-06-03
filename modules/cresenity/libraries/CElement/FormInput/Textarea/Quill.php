<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2019, 7:17:38 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

class CElement_FormInput_Textarea_Summernote extends CElement_FormInput_Textarea {

  
    public function __construct($id) {
        parent::__construct($id);
        CManager::registerModule('quill');
    }

    public function build() {
        $this->addClass('quill-control');
    }

   

    public function js($indent = 0) {


  
        $js = "";

        $js .= "
            var editor = new Quill('#" . $this->id . "');
       
      
        ";


        $js .= $this->jsChild();
        return $js;
    }

}
