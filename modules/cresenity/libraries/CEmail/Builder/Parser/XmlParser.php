<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Parser_XmlParser {

    /**
     *
     * @var string
     */
    protected $xml;
    protected $components = [];
    protected $addEmptyAttributes = true;
    protected $convertBooleans = true;
    protected $keepComments = true;
    protected $filePath = '.';
    protected $ignoreIncludes = false;
    protected $currentEndingTagIndexes = [];
    protected $endingTags = [];
    protected $parentNode = null;

    public function __construct($xml, $options) {
        $defaultOptions = array(
            'addEmptyAttributes' => true,
            'components' => [],
            'convertBooleans' => true,
            'keepComments' => true,
            'filePath' => '.',
            'ignoreIncludes' => false,
        );
        $options = array_merge($defaultOptions, $options);
        $this->xml = $xml;
        $this->addEmptyAttributes = carr::get($options, 'addEmptyAttributes', true);
        $this->components = carr::get($options, 'components', []);
        $this->convertBooleans = carr::get($options, 'convertBooleans', true);
        $this->keepComments = carr::get($options, 'keepComments', true);
        $this->filePath = carr::get($options, 'filePath', '.');
        $this->ignoreIncludes = carr::get($options, 'ignoreIncludes', false);


        $this->currentEndingTagIndexes = array(
            'startIndex' => 0,
            'endIndex' => 0,
        );
    }

    protected function findTag($tagName, $tree) {
        return carr::find($tree->children(), array('tagName' => $tagName));
    }

    public function onOpenTag($name, $attrs) {
        
    }

    public function parse() {
        $options = [];
        $parser = CParser::createHtmlParser($options);
        $parser->listen(CParser_HtmlParser_Event_OnEnd::class, function($event) {
          
        });
        
        
        $parser->write($this->xml);
        $parser->end();


        return $this->parentNode;
    }

}
