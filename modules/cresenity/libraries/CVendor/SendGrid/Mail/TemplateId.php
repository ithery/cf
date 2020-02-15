<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used to construct a TemplateId object for the /mail/send API call
 *
 * @package SendGrid\Mail
 */
class CVendor_SendGrid_Mail_TemplateId implements \JsonSerializable {

    /**
     * @var $template_id string The id of a template that you would like to use. If you use a
     * template that contains a subject and content (either text or html), you do
     * not need to specify those at the personalizations nor message level
     */
    private $template_id;

    /**
     * Optional constructor
     *
     * @param string|null $template_id The id of a template that you would like
     *                                 to use. If you use a template that contains
     *                                 a subject and content (either text or html),
     *                                 you do not need to specify those at the
     *                                 personalizations nor message level
     */
    public function __construct($template_id = null) {
        if (isset($template_id)) {
            $this->setTemplateId($template_id);
        }
    }

    /**
     * Add a template id to a TemplateId object
     *
     * @param string $template_id The id of a template that you would like
     *                            to use. If you use a template that contains
     *                            a subject and content (either text or html),
     *                            you do not need to specify those at the
     *                            personalizations nor message level
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setTemplateId($template_id) {
        if (!is_string($template_id)) {
            throw new CVendor_SendGrid_Mail_TypeException('$template_id must be of type string.');
        }

        $this->template_id = $template_id;
    }

    /**
     * Retrieve a template id from a TemplateId object
     *
     * @return string
     */
    public function getTemplateId() {
        return $this->template_id;
    }

    /**
     * Return an array representing a TemplateId object for the Twilio SendGrid API
     *
     * @return string
     */
    public function jsonSerialize() {
        return $this->getTemplateId();
    }

}
