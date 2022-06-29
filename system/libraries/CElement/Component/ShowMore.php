<?php

class CElement_Component_ShowMore extends CElement_Component {
    protected $showMoreLabel;

    protected $showLessLabel;

    protected $limit;

    protected $type;

    public function __construct($id = null) {
        parent::__construct($id);
        $this->showMoreLabel = '→ show more';
        $this->showLessLabel = '← show less';
        $this->limit = 120;
        $this->type = 'text';
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
        $this->setAttr('data-config', htmlspecialchars(json_encode($config), ENT_QUOTES, 'UTF-8'));
    }
}
