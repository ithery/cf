<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Symfony\Component\CssSelector\Node\Specificity;
use CParser_Css_Property as Property;

final class CParser_Css_Rule {

    /**
     * @var string
     */
    private $selector;

    /**
     * @var Property[]
     */
    private $properties;

    /**
     * @var Specificity
     */
    private $specificity;

    /**
     * @var integer
     */
    private $order;

    /**
     * Rule constructor.
     *
     * @param string      $selector
     * @param Property[]  $properties
     * @param Specificity $specificity
     * @param int         $order
     */
    public function __construct($selector, array $properties, Specificity $specificity, $order) {
        $this->selector = $selector;
        $this->properties = $properties;
        $this->specificity = $specificity;
        $this->order = $order;
    }

    /**
     * Get selector
     *
     * @return string
     */
    public function getSelector() {
        return $this->selector;
    }

    /**
     * Get properties
     *
     * @return Property[]
     */
    public function getProperties() {
        return $this->properties;
    }

    /**
     * Get specificity
     *
     * @return Specificity
     */
    public function getSpecificity() {
        return $this->specificity;
    }

    /**
     * Get order
     *
     * @return int
     */
    public function getOrder() {
        return $this->order;
    }

}
