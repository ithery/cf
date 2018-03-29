<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Summernote
 *
 * @author Hery Kurniawan
 * @since Jan 28, 2018, 9:43:02 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_Textarea_Summernote extends CElement_FormInput_Textarea {

    public function __construct($id) {
        parent::__construct($id);
        CManager::registerModule('summernote');
    }

    public function build() {
        $this->add_class('summernote-control');
    }

    public function js($indent = 0) {

        $js = "";

        $js .= "
            
        $('#" . $this->id . "').summernote({
            height: '300px'
	});
        
      
        ";


        $js .= parent::js();
        return $js;
    }

}
