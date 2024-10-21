<?php
use CApp_Navigation_Helper as Helper;

class CNavigation_Menu {
    /**
     * Menu data.
     *
     * @var array
     */
    protected $items;

    public function __construct($menu) {
        $this->items = $menu;
    }

    public function getItems() {
        return $this->items;
    }

    public function count() {
        return count($this->items);
    }

    public static function createItem($item) {
        return  new ENAdmin_Navigation_Item($item);
    }
}
