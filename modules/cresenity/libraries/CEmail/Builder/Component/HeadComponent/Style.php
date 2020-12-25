<?php

class CEmail_Builder_Component_HeadComponent_Style extends CEmail_Builder_Component_HeadComponent {
    protected static $tagName = 'c-style';
    protected static $endingTag = true;
    protected $allowedAttributes = [
        'inline' => 'string',
    ];

    public function handler() {
        $attr = $this->getAttribute('inline') === 'inline' ? 'inlineStyle' : 'style';

        $this->context->addHead($attr, $this->getContent());
    }
}
