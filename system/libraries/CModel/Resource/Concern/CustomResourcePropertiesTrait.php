<?php

trait CModel_Resource_Concern_CustomResourcePropertiesTrait {
    public function setCustomHeaders(array $customHeaders) {
        $this->setCustomProperty('custom_headers', $customHeaders);

        return $this;
    }

    public function getCustomHeaders() {
        return $this->getCustomProperty('custom_headers', []);
    }
}
