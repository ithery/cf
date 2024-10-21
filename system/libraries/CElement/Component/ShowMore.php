<?php

class CElement_Component_ShowMore extends CElement_Component {
    /**
     * @var string
     */
    protected $showMoreLabel;

    /**
     * @var string
     */
    protected $showLessLabel;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var string
     */
    protected $type;

    public function __construct($id = null) {
        parent::__construct($id);
        $this->showMoreLabel = '→ show more';
        $this->showLessLabel = '← show less';
        $this->limit = 50;
        $this->type = 'text';
    }

    public static function factory($id = null) {
        // @phpstan-ignore-next-line
        return new static($id);
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit($limit) {
        $this->limit = $limit;

        return $this;
    }

    protected function build() {
        $this->addClass('cres:element:component:ShowMore');
        $config = [
            'type' => $this->type,
            'limit' => (int) $this->limit,
            'more' => $this->showMoreLabel,
            'less' => $this->showLessLabel,

        ];
        /*
        {
            "type": "list",
            "limit": 5,
            "element": "li",
            "more": "↓ show more",
            "less": "↑ less",
            "number": true // adds the number of items to the button
        }
        */
        $this->setAttr('cres-element', 'component:ShowMore');
        $this->setAttr('cres-config', c::json($config));
    }
}
