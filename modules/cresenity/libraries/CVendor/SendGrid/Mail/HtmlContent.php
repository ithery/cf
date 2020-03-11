<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used to construct a Content object for the /mail/send API call
 *
 * @package SendGrid\Mail
 */
class CVendor_SendGrid_Mail_HtmlContent extends CVendor_SendGrid_Mail_Content
{
    /**
     * Create a Content object with a HTML mime type
     *
     * @param string $value HTML formatted content
     */
    public function __construct($value)
    {
        parent::__construct(CVendor_SendGrid_Mail_MimeType::HTML, $value);
    }
}