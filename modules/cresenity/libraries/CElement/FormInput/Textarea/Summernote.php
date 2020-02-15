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

    protected $toolbarType = 'default';
    protected $haveDragDrop = false;

    public function __construct($id) {
        parent::__construct($id);
        CManager::registerModule('summernote');
    }

    public function build() {
        $this->addClass('summernote-control');
    }

    public function setToolbarType($toolbarType) {
        $this->toolbarType = $toolbarType;
        return $this;
    }

    public function setDragDrop($bool = true) {
        $this->haveDragDrop = $bool;
        return $this;
    }

    protected function getToolbarJson($toolbarType = null) {
        if ($toolbarType == null) {
            $toolbarType = $this->toolbarType;
        }
        $json = '[]';
        switch ($toolbarType) {
            case 'standard':
                $json = "
                    [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']]
                    ]
                ";
                break;
            case 'non-video':
                $json = "
                    [
                        ['fontstyle', ['style']],
                        ['style', ['bold', 'underline', 'clear']],
                        ['fontfamily', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['media', ['link', 'picture']],
                        ['misc', ['fullscreen', 'codeview', 'help']]
                    ]
                ";
                break;
            case 'link-only':
                $json = "
                    [
                        ['media', ['link']],
                    ]
                ";
                break;
            case 'text-only':
                $json = "
                    [
                        ['fontstyle', ['style']],
                        ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                        ['font', ['fontname', 'fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['link', ['link']],
                        ['height', ['height']]
                    ]
                ";
                break;
    	    case 'text-media':
    		        $json = "
                        [
                            ['fontstyle', ['style']],
                            ['style', ['bold', 'italic', 'underline', 'clear']],
                            ['font', ['strikethrough', 'superscript', 'subscript']],
                            ['fontsize', ['fontname', 'fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['media', ['link', 'picture']],
                            ['height', ['height']],
                            ['others', ['codeview']]
                        ]
                    ";
                break;
            case 'text':
                $json = "
                    [
                        ['style', ['bold', 'italic', 'underline']],                        
                    ]
                ";
                break;
        }
        return $json;
    }

    public function js($indent = 0) {


        $additionalOptions = 'disableDragAndDrop: true,';
        if ($this->haveDragDrop) {
            $additionalOptions = 'disableDragAndDrop: false,';
        }
        if ($this->toolbarType != 'default') {
            $additionalOptions .= 'toolbar:' . $this->getToolbarJson() . ',';
        }
        $js = "";

        $js .= "
            
        $('#" . $this->id . "').summernote({
            height: '300px',
            " . $additionalOptions . "
            maximumImageFileSize:1024*1024, // 1 MB
            callbacks:{ 
                onImageUploadError: function(msg){ 
                    alert('Oops, something went wrong with image url'); 
                }
            }
	});
        
      
        ";


        $js .= parent::js();
        return $js;
    }

}
