<?php

class CEmail_Builder_Component_BodyComponent_Body extends CEmail_Builder_Component_BodyComponent {
    protected static $tagName = 'c-body';

    protected $allowedAttributes = [
        'width' => 'unit(px,%)',
        'background-color' => 'color',
        'padding-bottom' => 'unit(px,%)',
        'padding-left' => 'unit(px,%)',
        'padding-right' => 'unit(px,%)',
        'padding-top' => 'unit(px,%)',
        'padding' => 'unit(px,%){1,4}',
    ];

    protected $defaultAttributes = [
        'width' => '600px',
    ];

    public function getStyles() {
        return [
            'div' => [
                'background-color' => $this->getAttribute('background-color'),
                'padding' => $this->getAttribute('padding'),
                'padding-top' => $this->getAttribute('padding-top'),
                'padding-right' => $this->getAttribute('padding-right'),
                'padding-bottom' => $this->getAttribute('padding-bottom'),
                'padding-left' => $this->getAttribute('padding-left'),
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
