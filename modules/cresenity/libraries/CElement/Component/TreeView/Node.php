<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 28, 2020 
 * @license Ittron Global Teknologi
 */
class CElement_Component_TreeView_Node implements CInterface_Arrayable {

    /**
     *
     * @var string
     */
    protected $text;

    /**
     *
     * @var string
     */
    protected $icon;

    /**
     *
     * @var CElement_Component_TreeView_Node[] 
     */
    protected $childs;

    public static function createFromArray($array) {
        $title = carr::get($array, 'text', '');
        $childs = carr::get($array, 'children', []);
        return new static($title, $childs);
    }

    public function __construct($text, $childs = []) {
        $this->text = $text;

        foreach ($childs as $child) {
            $this->addChild($child);
        }
    }

    public function addChild($child) {
        if (!($child instanceof CElement_Component_TreeView_Node)) {
            if (is_array($child)) {
                $child = static::createFromArray($child);
            } else if (is_string($child)) {
                $child = new static($child);
            } else {
                throw new Exception('child is on bad format');
            }
        }
        $this->childs[] = $child;
    }

    public function getText() {
        return $this->text;
    }

    public function getIcon() {
        return $this->icon;
    }

    public function clear() {
        if ($this->childs != null) {
            foreach ($this->childs as $child) {
                $child->clear();
            }
        }
        unset($this->childs);
        $this->childs = [];
    }

    public function toArray() {
        return c::collect($this->childs)->map(function($node) {
                    return [
                        'text' => $node->getText(),
                        'icon' => $node->getIcon(),
                        'children' => $node->toArray(),
                    ];
                })->all();
    }

}
