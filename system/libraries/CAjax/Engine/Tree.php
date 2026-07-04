<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Ajax engine backing CElement_Tree: requires the files registered via
 * CElement_Tree::setCallback()'s $require argument, then invokes that
 * callback with ['operation' => ..., 'custom_field_data' => ...], returning
 * whatever the callback returns (auto-encoded to JSON by the response layer).
 */
class CAjax_Engine_Tree extends CAjax_Engine {
    /**
     * @return mixed
     */
    public function execute() {
        $data = $this->getData();

        $requires = carr::get($data, 'requires', []);
        foreach ($requires as $require) {
            if (file_exists($require)) {
                require_once $require;
            }
        }

        $args = [
            'operation' => carr::get($data, 'operation'),
            'custom_field_data' => carr::get($data, 'custom_field_data'),
        ];

        return $this->invokeCallback(carr::get($data, 'callback'), [$args]);
    }
}
