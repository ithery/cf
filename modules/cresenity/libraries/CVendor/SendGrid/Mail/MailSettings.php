<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used to construct a MailSettings object for the /mail/send API call
 *
 * A collection of different mail settings that you can use to specify how you would
 * like this email to be handled
 *
 * @package SendGrid\Mail
 */
class CVendor_SendGrid_Mail_MailSettings implements \JsonSerializable {

    /** @var $bcc CVendor_SendGrid_Mail_Bcc object */
    private $bcc;

    /** @var $bypass_list_management CVendor_SendGrid_Mail_BypassListManagement object */
    private $bypass_list_management;

    /** @var $footer CVendor_SendGrid_Mail_Footer object */
    private $footer;

    /** @var $sandbox_mode CVendor_SendGrid_Mail_SandBoxMode object */
    private $sandbox_mode;

    /** @var $spam_check CVendor_SendGrid_Mail_SpamCheck object */
    private $spam_check;

    /**
     * Optional constructor
     *
     * @param CVendor_SendGrid_Mail_BccSettings|null $bcc_settings BccSettings object
     * @param CVendor_SendGrid_Mail_BypassListManagement|null $bypass_list_management BypassListManagement
     *                                                          object
     * @param CVendor_SendGrid_Mail_Footer|null $footer Footer object
     * @param CVendor_SendGrid_Mail_SandBoxMode|null $sandbox_mode SandBoxMode object
     * @param CVendor_SendGrid_Mail_SpamCheck|null $spam_check SpamCheck object
     */
    public function __construct(
    $bcc_settings = null, $bypass_list_management = null, $footer = null, $sandbox_mode = null, $spam_check = null
    ) {
        if (isset($bcc_settings)) {
            $this->setBccSettings($bcc_settings);
        }
        if (isset($bypass_list_management)) {
            $this->setBypassListManagement($bypass_list_management);
        }
        if (isset($footer)) {
            $this->setFooter($footer);
        }
        if (isset($sandbox_mode)) {
            $this->setSandboxMode($sandbox_mode);
        }
        if (isset($spam_check)) {
            $this->setSpamCheck($spam_check);
        }
    }

    /**
     * Set the bcc settings on a MailSettings object
     *
     * @param CVendor_SendGrid_Mail_BccSettings|bool $enable The BccSettings object or an indication
     *                                 if the setting is enabled
     * @param string|null $email The email address that you would like
     *                                 to receive the BCC
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setBccSettings($enable, $email = null) {
        if ($enable instanceof CVendor_SendGrid_Mail_BccSettings) {
            $bcc = $enable;
            $this->bcc = $bcc;
            return;
        }
        if (!is_bool($enable)) {
            throw new CVendor_SendGrid_Mail_TypeException(
            '$enable must be an instance of SendGrid\Mail\BccSettings or of type bool.'
            );
        }
        $this->bcc = new CVendor_SendGrid_Mail_BccSettings($enable, $email);
    }

    /**
     * Retrieve the bcc settings from a MailSettings object
     *
     * @return CVendor_SendGrid_Mail_Bcc
     */
    public function getBccSettings() {
        return $this->bcc;
    }

    /**
     * Set bypass list management settings on a MailSettings object
     *
     * @param CVendor_SendGrid_Mail_BypassListManagement|bool $enable The BypassListManagement
     *                                          object or an indication
     *                                          if the setting is enabled
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setBypassListManagement($enable) {
        if ($enable instanceof CVendor_SendGrid_Mail_BypassListManagement) {
            $bypass_list_management = $enable;
            $this->bypass_list_management = $bypass_list_management;
            return;
        }
        if (!is_bool($enable)) {
            throw new CVendor_SendGrid_Mail_TypeException(
            '$enable must be an instance of SendGrid\Mail\BypassListManagement or of type bool.'
            );
        }
        $this->bypass_list_management = new CVendor_SendGrid_Mail_BypassListManagement($enable);
        return;
    }

    /**
     * Retrieve bypass list management settings from a MailSettings object
     *
     * @return CVendor_SendGrid_Mail_BypassListManagement
     */
    public function getBypassListManagement() {
        return $this->bypass_list_management;
    }

    /**
     * Set the footer settings on a MailSettings object
     *
     * @param CVendor_SendGrid_Mail_Footer|bool $enable The Footer object or an indication
     *                            if the setting is enabled
     * @param string|null $text The plain text content of your footer
     * @param string|null $html The HTML content of your footer
     *
     * @return null
     */
    public function setFooter($enable, $text = null, $html = null) {
        if ($enable instanceof CVendor_SendGrid_Mail_Footer) {
            $footer = $enable;
            $this->footer = $footer;
            return;
        }
        $this->footer = new CVendor_SendGrid_Mail_Footer($enable, $text, $html);
        return;
    }

    /**
     * Retrieve the footer settings from a MailSettings object
     *
     * @return Footer
     */
    public function getFooter() {
        return $this->footer;
    }

    /**
     * Set sandbox mode settings on a MailSettings object
     *
     * @param CVendor_SendGrid_Mail_SandBoxMode|bool $enable The SandBoxMode object or an
     *                                 indication if the setting is enabled
     *
     * @return null
     */
    public function setSandboxMode($enable) {
        if ($enable instanceof CVendor_SendGrid_Mail_SandBoxMode) {
            $sandbox_mode = $enable;
            $this->sandbox_mode = $sandbox_mode;
            return;
        }
        $this->sandbox_mode = new CVendor_SendGrid_Mail_SandBoxMode($enable);
        return;
    }

    /**
     * Retrieve sandbox mode settings on a MailSettings object
     *
     * @return CVendor_SendGrid_Mail_SandBoxMode
     */
    public function getSandboxMode() {
        return $this->sandbox_mode;
    }

    /**
     * Enable sandbox mode on a MailSettings object
     */
    public function enableSandboxMode() {
        $this->setSandboxMode(true);
    }

    /**
     * Disable sandbox mode on a MailSettings object
     */
    public function disableSandboxMode() {
        $this->setSandboxMode(false);
    }

    /**
     * Set spam check settings on a MailSettings object
     *
     * @param CVendor_SendGrid_Mail_SpamCheck|bool $enable The SpamCheck object or an
     *                                    indication if the setting is enabled
     * @param int $threshold The threshold used to determine if your
     *                                    content qualifies as spam on a scale
     *                                    from 1 to 10, with 10 being most strict,
     *                                    or most
     * @param string $post_to_url An Inbound Parse URL that you would like
     *                                    a copy of your email along with the spam
     *                                    report to be sent to
     *
     * @return null
     */
    public function setSpamCheck($enable, $threshold = null, $post_to_url = null) {
        if ($enable instanceof CVendor_SendGrid_Mail_SpamCheck) {
            $spam_check = $enable;
            $this->spam_check = $spam_check;
            return;
        }
        $this->spam_check = new CVendor_SendGrid_Mail_SpamCheck($enable, $threshold, $post_to_url);
        return;
    }

    /**
     * Retrieve spam check settings from a MailSettings object
     *
     * @return CVendor_SendGrid_Mail_SpamCheck
     */
    public function getSpamCheck() {
        return $this->spam_check;
    }

    /**
     * Return an array representing a MailSettings object for the Twilio SendGrid API
     *
     * @return null|array
     */
    public function jsonSerialize() {
        return array_filter(
                        [
                    'bcc' => $this->getBccSettings(),
                    'bypass_list_management' => $this->getBypassListManagement(),
                    'footer' => $this->getFooter(),
                    'sandbox_mode' => $this->getSandboxMode(),
                    'spam_check' => $this->getSpamCheck()
                        ], function ($value) {
                    return $value !== null;
                }
                ) ?: null;
    }

}
