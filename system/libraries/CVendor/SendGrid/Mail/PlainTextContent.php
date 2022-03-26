<?php



/**
 * This class is used to construct a Content object for the /mail/send API call
 *
 * @package SendGrid\Mail
 */
class CVendor_SendGrid_Mail_PlainTextContent extends CVendor_SendGrid_Mail_Content {

    /**
     * Create a Content object with a plain text mime type
     *
     * @param string $value plain text formatted content
     */
    public function __construct($value) {
        parent::__construct(CVendor_SendGrid_Mail_MimeType::TEXT, $value);
    }

}
