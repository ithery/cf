<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CParser_HtmlParser_TokenizerCallbackTrait {

    public function onattribdata($value) {
        $this->attributeValue .= $value;
    }

    public function onattribend() {
        if ($this->dispatcher->hasListeners(CParser_HtmlParser_Event_OnAttribute::class)) {

            $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnAttribute($this->attributeName, $this->attributeValue));
        }
        if (is_array($this->attributes) && !array_key_exists($this->attributeName, $this->attributes)) {
            $this->attributes[$this->attributeName] = $this->attributeValue;
        }
        $this->attributeName = "";
        $this->attributeValue = "";
    }

    public function onattribname($name) {
        if ($this->lowerCaseAttributeNames) {
            $name = strtolower($name);
        }
        $this->attributeName = $name;
    }

    public function oncdata($data) {
        $this . updatePosition(1);
        if ($this->getOption('xmlMode') || $this->getOption('recognizeCDATA')) {
            $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnCdataStart());
            $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnText($data));
            $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnCdataEnd());
        } else {
            $this->oncomment('[CDATA[' . $value . ']]');
        }
    }

    public function onclosetag($name) {
        $this->updatePosition(1);
        if ($this->lowerCaseTagNames) {
            $name = strtolower($name);
        }

        if (in_array($name, static::$foreignContextElements) || in_array($name, static::$htmlIntegrationElements)) {
            array_pop($this->foreignContext);
        }

        if (count($this->stack) && $this->getOption('xmlModel') || !in_array($name, static::$voidElements)) {
            $pos = -1;
            for ($i = count($this->stack) - 1; $i >= 0; $i--) {
                if ($this->stack[$i] == $name) {
                    $pos = $i;
                    break;
                }
            }
            if ($pos !== -1) {
                if ($this->dispatcher->hasListeners(CParser_HtmlParser_Event_OnCloseTag::class)) {
                    $pos = count($this->stack) - $pos;
                    while ($pos--) {
                        $lastStack = $this->stack[count($this->stack) - 1];
                        array_pop($this->stack);
                        $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnCloseTag($lastStack));
                    }
                }
            } else if ($name === "p" && !$this . getOption('xmlMode')) {
                $this->onopentagname($name);
                $this->closeCurrentTag();
            }
        } else if (!$this . getOption('xmlMode') && ($name === "br" || $name === "p")) {
            $this->onopentagname($name);
            $this->closeCurrentTag();
        }
    }

    public function oncomment($data) {
        $this->updatePosition(4);
        $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnComment($data));
        $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnCommentEnd());
    }

    public function ondeclaration($content) {
        if ($this->dispatcher->hasListeners(CParser_HtmlParser_Event_OnProcessingInstruction::class)) {
            $name = $this->getInstructionName($content);
            $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnProcessingInstruction($name, $content));
        }
    }

    public function onend() {
        if ($this->dispatcher->hasListeners(CParser_HtmlParser_Event_OnCloseTag::class)) {
            for ($i = count($this->stack); $i > 0; $i--) {
                $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnCloseTag($this->stack[$i]));
            }
        }

        $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnEnd());
    }

    public function onerror($error, $state) {
        if ($this->dispatcher->hasListeners(CParser_HtmlParser_Event_OnError::class)) {
            $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnError($error));
        } else {
            throw new Exception($error);
        }
    }

    public function onopentagend() {
        $this->updatePosition(1);
        if (is_array($this->attributes)) {
            if ($this->dispatcher->hasListeners(CParser_HtmlParser_Event_OnOpenTag::class)) {

                $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnOpenTag($this->tagname, $this->attributes));
            }
            $this->attributes = null;
        }
        if (
                !$this->getOption('xmlMode') &&
                $this->dispatcher->hasListeners(CParser_HtmlParser_Event_OnCloseTag::class) &&
                in_array($this->tagname, static::$voidElements)
        ) {
            $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnCloseTag($this->tagname));
        }
        $this->tagname = "";
    }

    public function onopentagname($name) {
        if ($this->lowerCaseTagNames) {
            $name = strtolower($name);
        }
        $this->tagname = $name;

        if (!$this->getOption('xmlMode') && array_key_exists($name, static::$openImpliesClose)) {
            $processing = true;

            while ($processing) {
                $el = carr::get($this->stack, count($this->stack) - 1);
                $recordOpenImpliesClose = carr::get(static::$openImpliesClose, $name);
                $processing = in_array($el, $recordOpenImpliesClose);
                if ($processing) {
                    $this->onclosetag($el);
                }
            }
        }
        if ($this->getOption('xmlMode') || !in_array($name, static::$voidElements)) {
            $this->stack[] = $name;
            if (in_array($name, static::$foreignContextElements)) {
                $this->foreignContext[] = true;
            } else if (in_array($name, static::$htmlIntegrationElements)) {
                $this->foreignContext[] = false;
            }
        }
        $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnOpenTagName($name));
        if ($this->dispatcher->hasListeners(CParser_HtmlParser_Event_OnOpenTag::class)) {
            $this->attributes = array();
        }
    }

    public function onprocessinginstruction($instruction) {
        if ($this->dispatcher->hasListeners(CParser_HtmlParser_Event_OnProcessingInstruction::class)) {
            $name = $this->getInstructionName($instruction);
            $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnProcessingInstruction($name, $instruction));
        }
    }

    public function onselfclosingtag() {
        $lastForeignContext = carr::get($this->foreignContext, count($this->foreignContext) - 1, false);
        if (
                $this->getOption('xmlMode') ||
                $this->getOption('recognizeSelfClosing') ||
                $lastForeignContext
        ) {
            $this->closeCurrentTag();
        } else {
            $this->onopentagend();
        }
    }

    public function ontext($value) {
        $this->updatePosition(1);

        $this->endIndex--;
        $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnText($value));
    }

}
