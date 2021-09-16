<?php

use CEmail_Builder_Helper as Helper;

class CEmail_Builder_Component_BodyComponent_Text extends CEmail_Builder_Component_BodyComponent {
    protected static $tagName = 'c-text';
    protected static $endingTag = true;
    protected $allowedAttributes = [
        'align' => 'enum(left,right,center,justify)',
        'background-color' => 'color',
        'color' => 'color',
        'container-background-color' => 'color',
        'font-family' => 'string',
        'font-size' => 'unit(px)',
        'font-style' => 'string',
        'font-weight' => 'string',
        'height' => 'unit(px,%)',
        'letter-spacing' => 'unitWithNegative(px,%)',
        'line-height' => 'unit(px,%,)',
        'padding-bottom' => 'unit(px,%)',
        'padding-left' => 'unit(px,%)',
        'padding-right' => 'unit(px,%)',
        'padding-top' => 'unit(px,%)',
        'padding' => 'unit(px,%){1,4}',
        'text-decoration' => 'string',
        'text-transform' => 'string',
        'vertical-align' => 'enum(top,bottom,middle)',
    ];
    protected $defaultAttributes = [
        'align' => 'left',
        'color' => '#000000',
        'font-family' => 'Ubuntu, Helvetica, Arial, sans-serif',
        'font-size' => '13px',
        'line-height' => '1',
        'padding' => '10px 25px',
    ];

    public function getStyles() {
        return [
            'text' => [
                'font-family' => $this->getAttribute('font-family'),
                'font-size' => $this->getAttribute('font-size'),
                'font-style' => $this->getAttribute('font-style'),
                'font-weight' => $this->getAttribute('font-weight'),
                'letter-spacing' => $this->getAttribute('letter-spacing'),
                'line-height' => $this->getAttribute('line-height'),
                'text-align' => $this->getAttribute('align'),
                'text-decoration' => $this->getAttribute('text-decoration'),
                'text-transform' => $this->getAttribute('text-transform'),
                'color' => $this->getAttribute('color'),
                'height' => $this->getAttribute('height'),
            ],
        ];
    }

    public function renderContent() {
        return '
      <div' . $this->htmlAttributes(['style' => 'text']) . '>' . $this->getContent() . '</div>
    ';
    }

    public function render() {
        $height = $this->getAttribute('height');
        if (strlen($height) == 0) {
            return $this->renderContent();
        }
        $openTag = '
          <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td height="' . $height . '" style="vertical-align:top;height:' . $height . ';">
        ';
        $closeTag = '
          </td></tr></table>
        ';
        return Helper::conditionalTag($openTag) . $this->renderContent() . Helper::conditionalTag($closeTag);
    }
}
