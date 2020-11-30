<?php

/**
 * Description of S3DoesntSupportMultipleFileUploads
 *
 * @author Hery
 */
class CComponent_Exception_S3DoesntSupportMultipleFileUploads extends \Exception {

    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct() {
        parent::__construct(
                "S3 temporary file upload driver only supports single file uploads. Remove the [multiple] HTML attribute from your input tag."
        );
    }

}
