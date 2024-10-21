<?php

class CValidation_Rule_ImageFile extends CValidation_Rule_File {
    /**
     * Create a new image file rule instance.
     *
     * @return void
     */
    public function __construct() {
        $this->rules('image');
    }

    /**
     * The dimension constraints for the uploaded file.
     *
     * @param \CValidation_Rule_Dimension $dimensions
     */
    public function dimensions($dimensions) {
        $this->rules($dimensions);

        return $this;
    }
}
