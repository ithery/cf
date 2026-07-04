<?php

/**
 * Renders a jstree-backed file/folder tree element that fetches nodes and
 * content via ajax callbacks. Rendering/behavior is handled client-side by
 * cres.js (see media/js/cres/src/element/component/Tree), the same way
 * CElement_Component_Image and CElement_Component_Gallery work: this class
 * only builds the markup and passes config via the `cres-config` attribute.
 */
class CElement_Tree extends CElement_Component {
    use CTrait_Compat_Element_Tree;

    /**
     * @var callable|null
     */
    protected $callback;

    /**
     * List of file paths to require_once before invoking the callback.
     *
     * @var array
     */
    protected $requires;

    /**
     * @var array
     */
    protected $custom_field_data = [];

    /**
     * @param string|null $id
     *
     * @return void
     */
    public function __construct($id = null) {
        parent::__construct($id);

        $this->callback = null;
        $this->requires = [];
        c::manager()->registerModule('jstree');
    }

    /**
     * @param string|null $id
     *
     * @return static
     */
    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    /**
     * @param array $custom_field_data
     *
     * @return $this
     */
    public function setCustomFieldData($custom_field_data) {
        $this->custom_field_data = $custom_field_data;

        return $this;
    }

    /**
     * @param callable          $callback
     * @param array|string|null $require  one or more file paths to require_once before invoking the callback
     *
     * @return $this
     */
    public function setCallback(callable $callback, $require = null) {
        $this->callback = $callback;
        if ($require != null) {
            if (!is_array($require)) {
                $require = [$require];
            }
            foreach ($require as $req) {
                $this->requires[] = $req;
            }
        }

        return $this;
    }

    /**
     * @param string $operation 'get_node' or 'get_content'
     *
     * @return string
     */
    protected function makeAjaxUrl($operation) {
        return CAjax::createMethod()
            ->setType(CAjax::TYPE_TREE)
            ->setData('callback', $this->callback)
            ->setData('requires', $this->requires)
            ->setData('operation', $operation)
            ->setData('custom_field_data', $this->custom_field_data)
            ->makeUrl();
    }

    /**
     * @return void
     */
    protected function build() {
        parent::build();

        $this->addClass('cres:element:component:Tree');
        $this->setAttr('cres-element', 'component:Tree');
        $this->setAttr('cres-config', c::json([
            'nodeUrl' => $this->makeAjaxUrl('get_node'),
            'contentUrl' => $this->makeAjaxUrl('get_content'),
        ]));

        $this->addDiv()->addClass('cres-tree-nav');
        $this->addDiv()->addClass('cres-tree-content');
    }
}
