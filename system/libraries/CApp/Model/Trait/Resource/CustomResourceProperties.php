<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CApp_Model_Trait_Resource_CustomResourceProperties {
    public function setCustomHeaders(array $customHeaders) {
        $this->setCustomProperty('custom_headers', $customHeaders);

        return $this;
    }

    public function getCustomHeaders() {
        return $this->getCustomProperty('custom_headers', []);
    }
}
