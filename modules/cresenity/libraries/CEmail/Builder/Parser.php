<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Parser {

    /**
     *
     * @var CEmail_Builder_Node
     */
    protected $node;

    /**
     *
     * @var array 
     */
    protected $globalData = [];
    protected $errors = [];
    protected $content = '';
    protected $context = null;

    public function __construct($cml, $options = []) {
        $this->content = '';
        $this->errors = [];
        $defaultFonts = [];
        $defaultFonts['Open Sans'] = 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,700';
        $defaultFonts['Droid Sans'] = 'https://fonts.googleapis.com/css?family=Droid+Sans:300,400,500,700';
        $defaultFonts['Lato'] = 'https://fonts.googleapis.com/css?family=Lato:300,400,500,700';
        $defaultFonts['Roboto'] = 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700';
        $defaultFonts['Ubuntu'] = 'https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700';

        $this->node = $cml;
        $beautify = carr::get($options, 'beautify', false);
        $fonts = carr::get($options, 'fonts', $defaultFonts);
        $keepComments = carr::get($options, 'keepComments', false);
        $minify = carr::get($options, 'minify', false);
        $minifyOptions = carr::get($options, 'minifyOptions', []);
        $validationLevel = carr::get($options, 'validationLevel', 'soft');
        $filePath = carr::get($options, 'filePath', '.');



        if (is_string($this->node)) {
            $parserOptions = [];
            $parserOptions['keepComments'] = $keepComments;
            $parserOptions['components'] = CEmail::builder()->components();
            $parserOptions['filePath'] = $filePath;
            
            
            $cmlParser = new CEmail_Builder_Parser_CmlParser($this->node, $parserOptions);
            $this->node = $cmlParser->parse();
        }


        $this->globalData = array();
        $this->globalData['backgroundColor'] = '';
        $this->globalData['breakpoiunt'] = '480px';
        $this->globalData['classes'] = [];
        $this->globalData['classesDefault'] = [];
        $this->globalData['defaultAttributes'] = [];
        $this->globalData['fonts'] = $fonts;
        $this->globalData['inlineStyle'] = [];
        $this->globalData['headStyle'] = [];
        $this->globalData['componentHeadStyle'] = [];
        $this->globalData['headRaw'] = [];
        $this->globalData['mediaQueries'] = [];
        $this->globalData['preview'] = '';
        $this->globalData['style'] = [];
        $this->globalData['title'] = '';
        $this->globalData['forceOWADesktop'] = CF::get($this->node, 'attributes.owa', 'mobile') === 'desktop';
        $this->globalData['lang'] = CF::get($this->node, 'attributes.lang');

        $this->context = new CEmail_Builder_Context();
    }

    public function parse() {

        $cHead = carr::find($this->node->children, ['tagName' => 'c-head']);
        //$this->globalDatas['headRaw'] = $this->processing($cHead, $headHelpers);

        $content = $this->getContent();
        $options = $this->globalData;


        $renderer = new CEmail_Builder_Renderer($content, $options);
        return trim($renderer->render());
    }

    protected function getContent() {
        if ($this->node == null) {
            return null;
        }
        $node = $this->node;
        $cBody = carr::find($this->node->children, ['tagName' => 'c-body']);
        $name = $cBody->getComponentName();

        $options = [];
        $options['attributes'] = $node->getAttributes();
        $options['children'] = $node->getChildren();
        $options['name'] = $name;
        $options['context']=$this->context;
        $options['content']=$node->getContent();
        //$options = $callbackParseCML();
        //$options['context'] = $context;
        $component = CEmail::builder()->createComponent($name, $options);

        return $component->render();
    }

    public function processing($node, $context, $callbackParseCML = null) {

        $name = $node->tagName;
        if (cstr::startsWith($name, 'c-')) {
            $name = substr($name, '2');
        }

        if ($callbackParseCML == null) {
            $callbackParseCML = array('c', 'identity');
        }

        $options = $callbackParseCML();
        $options['context'] = $context;

        $component = CEmail::builder()->initComponent($name, $options);
    }

}
