<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 2, 2019, 12:23:10 AM
 */
trait CApp_Model_Trait_Resource_CustomResourceProperties {
    public function setCustomHeaders(array $customHeaders) {
        $this->setCustomProperty('custom_headers', $customHeaders);
        return $this;
    }

    public function getCustomHeaders() {
        return $this->getCustomProperty('custom_headers', []);
    }
}
