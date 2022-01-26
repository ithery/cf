<?php
/**
 * @method CEmail_Builder_Node setBackgroundColor($param)
 * @method CEmail_Builder_Node setColor($param)
 * @method CEmail_Builder_Node setFontFamily($param)
 * @method CEmail_Builder_Node setFontSize($param)
 * @method CEmail_Builder_Node setPadding($param)
 * @method CEmail_Builder_Node setMargin($param)
 * @method CEmail_Builder_Node setLineHeight($param)
 */
class CEmail_Builder_Node {
    use CEmail_Builder_Trait_NodeTrait;

    public $parent = null;

    public $line = null;

    public $children = [];

    public $filePath = null;

    public $absoluteFilePath = null;

    public $tagName = null;

    public $attributes = [];

    public $content = '';

    public function __construct($options = []) {
        $this->tagName = carr::get($options, 'tagName');
        $this->attributes = carr::get($options, 'attributes', []);
    }

    public function getComponentName() {
        $name = $this->tagName;

        if (cstr::startsWith($name, 'c-')) {
            $name = substr($name, '2');
        }

        return $name;
    }

    public function getChildren() {
        return $this->children;
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function getTagName() {
        return $this->tagName;
    }

    public function getContent() {
        return $this->content;
    }

    public function setAttr($key, $value) {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @param CEmail_Builder_Node|string $node
     *
     * @return $this
     */
    public function add($node) {
        if ($node instanceof CEmail_Builder_Node) {
            $this->children[] = $node;
        } elseif (is_string($node)) {
            $this->content .= $node;
        } else {
            throw new Exception('Invalid argument for add method on object node');
        }

        return $this;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call($name, $arguments) {
        if (cstr::startsWith($name, 'set')) {
            $attrCamel = substr($name, 3);
            $attrSnake = cstr::snake($attrCamel, '-');

            return $this->setAttr($attrSnake, carr::get($arguments, 0));
        }

        throw new Exception('undefined method ' . $name . ' called for node');
    }
}
