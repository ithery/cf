<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CEmail_Builder_Parser_CmlParserSubscriberTrait {

    public static $i = 0;

    public function onOpenTag(CParser_HtmlParser_Event_OnOpenTag $event) {
        $name = $event->name;
        $attributes = $event->attributes;
        $isAnEndingTag = in_array($name, $this->endingTags);
        if ($this->inEndingTag > 0) {
            if ($isAnEndingTag) {
                $this->inEndingTag += 1;
                return;
            }
        }

        if ($isAnEndingTag) {
            $this->inEndingTag += 1;

            if ($this->inEndingTag === 1) { // we're entering endingTag
                $this->currentEndingTagIndexes['startIndex'] = $this->parser->getStartIndex();
                $this->currentEndingTagIndexes['endIndex'] = $this->parser->getEndIndex();
            }
        }

        $line = carr::findLastIndex($this->lineIndexes, function($i) {
                    return $i < $this->parser->getStartIndex();
                }) + 1;

        /*
          if ($name === 'c-include' && !$this->ignoreIncludes) {
          $this->inInclude = true
          $this->handleInclude(decodeURIComponent(attrs.path), line)
          return
          }
         * 
         */




        if ($this->convertBooleans) {
            // "true" and "false" will be converted to bools
            $attributes = $this->convertBooleansOnAttrs($attributes);
        }

//        $newNode = [
//            'file' => $this->filePath,
//            'absoluteFilePath' => $this->resolvePath($this->filePath),
//            'line' => $line,
//            'includedIn' => $this->includedIn,
//            'parent' => $this->currentNode,
//            'tagName' => $name,
//            'attributes' => $attributes,
//            'children' => [],
//        ];
        $newNodeObject = new CEmail_Builder_Node();
        $newNodeObject->line = $line;
        $newNodeObject->parent = &$this->currentNode;
        $newNodeObject->tagName = $name;
        $newNodeObject->attributes = $attributes;
        $newNodeObject->children = [];

        if ($this->currentNode) {


            $this->currentNode->children[] = $newNodeObject;
        } else {
            $this->parentNode = &$newNodeObject;
        }

        $this->currentNode = &$newNodeObject;
    }

    public function onCloseTag(CParser_HtmlParser_Event_OnCloseTag $event) {

        $name = $event->name;

        if (in_array($name, $this->endingTags)) {
            $this->inEndingTag -= 1;

            if (!$this->inEndingTag) { // we're getting out of endingTag
                // if self-closing tag we don't get the content
                if (!$this->isSelfClosing($this->currentEndingTagIndexes, $this->parser)) {
                    $partialVal = trim(substr($this->cml, currentEndingTagIndexes['endIndex'] + 1, $this->parser->getEndIndex() - (currentEndingTagIndexes['endIndex'] + 1)));
                    $val = substr($partialVal, 0, strrpos($partialVal, '</' . $name));

                    if ($val) {
                        $this->currentNode->content = trim($val);
                    }
                }
            }
        }

        if ($this->inEndingTag > 0) {
            return;
        }

        if ($this->inInclude) {
            $this->inInclude = false;
        }

        // for includes, setting cur is handled in handleInclude because when there is
        // only mj-head in include it doesn't create any elements, so setting back to parent is wrong
        if ($name !== 'mj-include') {
            if ($this->currentNode != null) {


                //cdbg::varDump($this->currentNode->parent->tagName);
                $this->currentNode = &$this->currentNode->parent;
            }
            //$this->currentNode = ($this->currentNode && $this->currentNode->parent) | null;
        }
    }

}
