<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 20, 2019, 3:44:27 PM
 */
class CObservable_Listener_Handler_ToggleActiveHandler extends CObservable_Listener_Handler {
    use CObservable_Listener_Handler_Trait_TargetHandlerTrait;
    use CObservable_Listener_Handler_Trait_SelectorHandlerTrait;

    /**
     * @var string
     */
    protected $itemsSelector;

    /**
     * @var string
     */
    protected $toggleClass;

    public function __construct($listener) {
        parent::__construct($listener);
        $this->target = $this->owner;
        $this->name = 'ToggleActive';
        $this->itemsSelector = null;
        $this->toggleClass = 'active';
    }

    public function setItemsSelector($selector) {
        $this->itemsSelector = $selector;

        return $this;
    }

    public function setToggleClass($class) {
        $this->toggleClass = $class;

        return $this;
    }

    public function js() {
        $js = '';

        if ($this->itemsSelector) {
            $js .= "jQuery('" . $this->itemsSelector . "').removeClass('" . $this->toggleClass . "');";
        } else {
            $js .= "jQuery('" . $this->getSelector() . "').parent().children().removeClass('" . $this->toggleClass . "');";
        }
        $js .= "jQuery('" . $this->getSelector() . "').addClass('" . $this->toggleClass . "');";

        return $js;
    }
}
