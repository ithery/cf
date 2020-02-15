<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2019, 7:17:38 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_Textarea_Quill extends CElement_Element_Div {
    use CTrait_Element_Property_Value;
    
    protected $theme;
    protected $toolbar;

    public function __construct($id) {
        parent::__construct($id);
        CManager::registerModule('quill');
        $this->theme = 'snow';
        $this->toolbar = $this->setToolbar('full');
    }

    public function setTheme($theme) {
        $this->theme = $theme;
        return $this;
    }
    public function setToolbarType($toolbar) {
        $this->setToolbar($toolbar);
    }
    public function setToolbar($toolbar) {
        if (!is_array($toolbar)) {
            $toolbarValue = $this->getToolbarJson($toolbar);
            if ($toolbarValue != null) {
                $toolbarValue = @json_decode($toolbarValue, true);
            }
            if ($toolbarValue == null) {
                $toolbarValue = @json_decode($toolbar, true);
            }
            $toolbar = $toolbarValue;
        }
        $this->toolbar = $toolbar;
    }

    protected function getToolbarJson($toolbarType = null) {

        $json = null;
        switch ($toolbarType) {
            case 'full':
                $json = "
                    [
                        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                        ['blockquote', 'code-block'],

                        [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                        [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                        [{ 'direction': 'rtl' }],                         // text direction

                        [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

                        [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                        [{ 'font': [] }],
                        [{ 'align': ['center'] }],

                        ['clean']                                         // remove formatting button
                    ]
                ";
                break;
        }
        return $json;
    }

    
    public function build() {
        $this->addClass('quill-control');
        $this->add($this->value);
    }

    public function js($indent = 0) {
        switch ($this->theme) {
            case 'bubble':
                CManager::registerCss('plugins/quill/quill.bubble.css');
                break;
            case 'snow':
                CManager::registerCss('plugins/quill/quill.snow.css');
                break;
        }

        $jsOptions = '';
        $jsOptions .= "{";
        $jsOptions .= "theme: '" . $this->theme . "'";

        $jsOptions .= "}";

        $js = "
            var editor = new Quill('#" . $this->id . "'," . $jsOptions . ");
       
      
        ";


        $js .= $this->jsChild();
        return $js;
    }

}
