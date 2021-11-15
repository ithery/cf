<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 7, 2018, 5:28:34 AM
 */
class CElement_Component_Accordion_Item extends CElement_Component {
    /**
     * @var CElement_Component_Accordion_Item_Header
     */
    protected $header;

    /**
     * @var CElement_Element_Div
     */
    protected $body;

    /**
     * @var CElement_Element_Div
     */
    protected $cardBody;

    /**
     * @var bool
     */
    protected $active;

    public function __construct($id) {
        parent::__construct($id);
        $this->header = CElement_Factory::createComponent('Accordion_Item_Header');
        $this->add($this->header);

        $this->body = $this->addDiv()->addClass('component-accordion-item');
        $this->cardBody = $this->body->addDiv()->addClass('component-accordion-item-body card-body');
        $this->wrapper = $this->cardBody;
        $this->header->setTargetBody($this->body->id);
        $this->addClass('component-accordion-item card');
        $this->body->addClass('collapse');
        $this->active = false;
    }

    /**
     * @return type
     */
    public function header() {
        return $this->header;
    }

    /**
     * Set the title of the accordion item.
     *
     * @param string $title
     * @param string $lang
     *
     * @return $this
     */
    public function setTitle($title, $lang = true) {
        $this->header()->setTitle($title, $lang);

        return $this;
    }

    public function setActive($bool = true) {
        $this->active = $bool;

        return $this;
    }

    public function build() {
        $this->body->setAttr('data-parent', $this->parent->id);
        if ($this->active) {
            $this->body->addClass('show');
        }
    }
}
