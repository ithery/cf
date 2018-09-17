<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 7, 2018, 7:50:27 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_PrismCode extends CElement_Component {

    protected $prismLanguage = 'php';
    protected $prismTheme = 'okaidia';
    protected $codeElement;
    
    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->tag = 'pre';
        $this->codeElement = $this->addCode();
        $this->wrapper = $this->codeElement;
        $this->isIndent=false;
    }
    public function setLanguage($lang) {
        $this->prismLanguage = $lang;
        return $this;
    }

    public function setTheme($theme) {
        $this->prismTheme = $theme;
        return $this;
    }

    protected function build() {
        $cs = CManager::clientScript();
        $cs->registerJsFile('plugins/prism/prism.min.js');
        $cs->registerJsFile('plugins/prism/components/prism-'.$this->prismLanguage.'.js');
        $cs->registerCssFile('plugins/prism/themes/prism-'.$this->prismTheme.'.css');
        $this->codeElement->addClass('language-'.$this->prismLanguage);
        $this->addJs('Prism.highlightAll();');
    }
    
    

}
