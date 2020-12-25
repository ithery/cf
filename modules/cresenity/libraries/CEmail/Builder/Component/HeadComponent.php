<?php

use CEmail_Builder_Helper as Helper;

class CEmail_Builder_Component_HeadComponent extends CEmail_Builder_Component {
    public function handlerChildren($options = []) {
        $childrens = $this->getChildren();
        if ($childrens == null) {
            return '';
        }
        $attributes = carr::get($options, 'attributes', []);
        $index = 0;
        return carr::map($childrens, function ($children) use ($attributes) {
            $component = $children;
            if ($children instanceof CEmail_Builder_Node) {
                $options = [];
                $options['children'] = $children->getChildren();
                $options['attributes'] = array_merge($attributes, $children->getAttributes());
                $options['context'] = $this->getChildContext();
                $options['name'] = $children->getComponentName();
                $options['content'] = $children->getContent();

                $component = CEmail::Builder()->createComponent($children->getComponentName(), $options);
            }
            if (!$component) {
                // eslint-disable-next-line no-console
                //console.error(`No matching component for tag : ${children.tagName}`)
                throw new Exception('No matching component for tag : ' . $children->tagName);
                return null;
            }

            if (method_exists($component, 'handler')) {
                $component->handler();
            }
            if (method_exists($component, 'render')) {
                return $component->render();
            }
            return null;
        });
    }
}
