<?php

class CElement_Component_ShowMore extends CElement_Component {
    protected function build() {
        $this->addClass('cres:element:component:ShowMore');
        $config = [
            'type' => 'text',
            'limit' => 120,
            'more' => '→ show more',
            'less' => '← read less',

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
