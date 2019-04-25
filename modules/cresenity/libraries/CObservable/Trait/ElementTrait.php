<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 23, 2019, 11:43:39 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CObservable_Trait_ElementTrait {

    /**
     * Add Div &lt;div&gt
     *
     * @param string $id optional
     * @return  CElement_Element_Div  Div Element
     */
    public function addDiv($id = "") {
        $element = CElement_Factory::createElement('div', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Label &lt;label&gt
     *
     * @param string $id optional
     * @return  CElement_Element_Label  Label Element
     */
    public function addLabel($id = "") {
        $element = CElement_Factory::createElement('label', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Anchor Element &lt;a&gt
     *
     * @param string $id optional
     * @return  CElement_Element_A  Anchor Element
     */
    public function addA($id = "") {
        $element = CElement_Factory::createElement('a', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Heading 1 Element &lt;h1&gt
     *
     * @param string $id optional
     * @return  CElement_Element_H1  Heading 1 Element
     */
    public function addH1($id = "") {
        $element = CElement_Factory::createElement('h1', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Heading 2 Element &lt;h2&gt
     *
     * @param string $id optional
     * @return  CElement_Element_H2  Heading 2 Element
     */
    public function addH2($id = "") {
        $element = CElement_Factory::createElement('h2', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Heading 3 Element &lt;h3&gt
     *
     * @param string $id optional
     * @return  CElement_Element_H3  Heading 3 Element
     */
    public function addH3($id = "") {
        $element = CElement_Factory::createElement('h3', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Heading 4 Element &lt;h4&gt
     *
     * @param string $id optional
     * @return  CElement_Element_H4  Heading 4 Element
     */
    public function addH4($id = "") {
        $element = CElement_Factory::createElement('h4', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Heading 5 Element &lt;h5&gt
     *
     * @param string $id optional
     * @return  CElement_Element_H5  Heading 5 Element
     */
    public function addH5($id = "") {
        $element = CElement_Factory::createElement('h5', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Heading 6 Element &lt;h6&gt
     *
     * @param string $id optional
     * @return  CElement_Element_H6  Heading 6 Element
     */
    public function addH6($id = "") {
        $element = CElement_Factory::createElement('h6', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Paragraph Element &lt;p&gt
     *
     * @param string $id optional
     * @return  CElement_Element_P  Paragraph Element
     */
    public function addP($id = "") {
        $element = CElement_Factory::createElement('p', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Ordered List Element &lt;ol&gt
     *
     * @param string $id optional
     * @return  CElement_Element_Ol  Ordered List Element
     */
    public function addOl($id = "") {
        $element = CElement_Factory::createElement('ol', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Unordered List Element &lt;ul&gt
     *
     * @param string $id optional
     * @return  CElement_Element_Ul  Unordered List Element
     */
    public function addUl($id = "") {
        $element = CElement_Factory::createElement('ul', $id);
        //$element = CUlElement::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Table Row Element &lt;tr&gt
     *
     * @param string $id optional
     * @return  CElement_Element_Tr  Table Row Element
     */
    public function addTr($id = "") {
        $element = CElement_Factory::createElement('tr', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Table Cell Element &lt;td&gt
     *
     * @param string $id optional
     * @return  CElement_Element_Td  Table Cell Element
     */
    public function addTd($id = "") {
        $element = CElement_Factory::createElement('td', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Code Element &lt;ul&gt
     *
     * @param string $id optional
     * @return  CElement_Element_Code  Code Element
     */
    public function addCode($id = "") {
        $element = CElement_Factory::createElement('code', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add List Item Element &lt;li&gt
     * 
     * @param string $id
     * @return CElement_Element_Ol List Item Element
     */
    public function addLi($id = "") {
        $element = CElement_Factory::createElement('li', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Iframe Element &lt;iframe&gt
     * 
     * @param string $id
     * @return CElement_Element_Iframe Iframe Element
     */
    public function addIframe($id = "") {
        $element = CElement_Factory::createElement('iframe', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Canvas Element &lt;canvas&gt
     * 
     * @param string $id
     * @return CElement_Element_Canvas Canvas Element
     */
    public function addCanvas($id = "") {
        $element = CElement_Factory::createElement('canvas', $id);
        $this->wrapper->add($element);
        return $element;
    }
    
    
    /**
     * Add Canvas Element &lt;canvas&gt
     * 
     * @param string $id
     * @return CElement_Element_Canvas Canvas Element
     */
    public function addImg($id = "") {
        $element = CElement_Factory::createElement('img', $id);
        $this->wrapper->add($element);
        return $element;
    }
    

}
