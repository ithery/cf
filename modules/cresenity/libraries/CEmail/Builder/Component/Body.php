<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Component_Body extends CEmail_Builder_BodyComponent {

    protected $allowedAttributes = array(
        'width' => 'unit(px,%)',
        'background-color' => 'color',
    );
    protected $defaultAttributes = array(
        'width' => '600px',
    );

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
