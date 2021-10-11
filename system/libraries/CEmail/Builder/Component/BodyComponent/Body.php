<?php

class CEmail_Builder_Component_BodyComponent_Body extends CEmail_Builder_Component_BodyComponent {
    protected static $tagName = 'c-body';

    protected $allowedAttributes = [
        'width' => 'unit(px,%)',
        'background-color' => 'color',
    ];
    protected $defaultAttributes = [
        'width' => '600px',
    ];

    public function getStyles() {
        return [
            'div' => [
                'background-color' => $this->getAttribute('background-color'),
            ],
        ];
    }

    public function getChildContext() {
        $context = clone $this->context;
        $width = $this->getAttribute('width');

        $context->set('containerWidth', $width);
        return $context;
    }

    public function render() {
        $backgroundColor = $this->getAttribute('background-color');
        if (strlen($backgroundColor) > 0) {
            $this->context->setbackgroundColor($backgroundColor);
        }

        return '
      <div' . $this->htmlAttributes(['class' => $this->getAttribute('css-class'), 'style' => 'div']) . '>
        ' . $this->renderChildren() . '
      </div>
    ';
    }
}
