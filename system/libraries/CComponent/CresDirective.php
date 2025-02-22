<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_CresDirective implements CInterface_Htmlable {
    public function __construct($name, $directive, $value) {
        $this->name = $name;
        $this->directive = $directive;
        $this->value = $value;
    }

    public function name() {
        return $this->name;
    }

    public function directive() {
        return $this->directive;
    }

    public function value() {
        return $this->value;
    }

    public function modifiers() {
        return c::str($this->directive)
            ->replace("cres:{$this->name}", '')
            ->explode('.')
            ->filter()->values();
    }

    public function hasModifier($modifier) {
        return $this->modifiers()->contains($modifier);
    }

    public function toHtml() {
        return (new CView_ComponentAttributeBag([$this->directive => $this->value]))->toHtml();
    }

    public function toString() {
        return (string) $this;
    }

    public function __toString() {
        return (string) $this->value;
    }
}
