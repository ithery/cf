<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used to construct a SubscriptionTracking object for
 * the /mail/send API call
 *
 * Allows you to insert a subscription management link at the bottom
 * of the text and html bodies of your email. If you would like to
 * specify the location of the link within your email, you may use
 * the substitution_tag
 *
 * @package SendGrid\Mail
 */
class CVendor_SendGrid_Mail_SubscriptionTracking implements \JsonSerializable {

    /** @var $enable bool Indicates if this setting is enabled */
    private $enable;

    /**
     * @var $text string Text to be appended to the email, with the
     * subscription tracking link. You may control where the
     * link is by using the tag <% %>
     */
    private $text;

    /**
     * @var $htmlstring string to be appended to the email, with the
     * subscription tracking link. You may control where the
     * link is by using the tag <% %>
     */
    private $html;

    /**
     * @var $substitution_tag string A tag that will be replaced with the
     * unsubscribe URL. for example: [unsubscribe_url]. If
     * this parameter is used, it will override both the text
     * and html parameters. The URL of the link will be placed
     * at the substitution tag’s location, with no additional
     * formatting
     */
    private $substitution_tag;

    /**
     * Optional constructor
     *
     * @param bool|null $enable Indicates if this setting is enabled
     * @param string|null $text Text to be appended to the email, with
     *                                      the subscription tracking link. You may
     *                                      control where the link is by using the
     *                                      tag <% %>
     * @param string|null $html HTML to be appended to the email, with
     *                                      the subscription tracking link. You may
     *                                      control where the link is by using the
     *                                      tag <% %>
     * @param string|null $substitution_tag A tag that will be replaced with the
     *                                      unsubscribe URL. For example:
     *                                      [unsubscribe_url]. If this parameter
     *                                      is used, it will override both the text
     *                                      and html parameters. The URL of the link
     *                                      will be placed at the substitution tag’s
     *                                      location, with no additional formatting
     */
    public function __construct(
    $enable = null, $text = null, $html = null, $substitution_tag = null
    ) {
        if (isset($enable)) {
            $this->setEnable($enable);
        }
        if (isset($text)) {
            $this->setText($text);
        }
        if (isset($html)) {
            $this->setHtml($html);
        }
        if (isset($substitution_tag)) {
            $this->setSubstitutionTag($substitution_tag);
        }
    }

    /**
     * Update the enable setting on a SubscriptionTracking object
     *
     * @param bool $enable Indicates if this setting is enabled
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setEnable($enable) {
        if (!is_bool($enable)) {
            throw new CVendor_SendGrid_Mail_TypeException('$enable must be of type bool.');
        }
        $this->enable = $enable;
    }

    /**
     * Retrieve the enable setting from a SubscriptionTracking object
     *
     * @return bool
     */
    public function getEnable() {
        return $this->enable;
    }

    /**
     * Add text to a SubscriptionTracking object
     *
     * @param string $text Text to be appended to the email, with
     *                     the subscription tracking link. You may
     *                     control where the link is by using the
     *                     tag <% %>
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setText($text) {
        if (!is_string($text)) {
            throw new CVendor_SendGrid_Mail_TypeException('$text must be of type string.');
        }
        $this->text = $text;
    }

    /**
     * Retrieve text from a SubscriptionTracking object
     *
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Add HTML to a SubscriptionTracking object
     *
     * @param string $html HTML to be appended to the email, with
     *                     the subscription tracking link. You may
     *                     control where the link is by using the
     *                     tag <% %>
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setHtml($html) {
        if (!is_string($html)) {
            throw new CVendor_SendGrid_Mail_TypeException('$html must be of type string.');
        }
        $this->html = $html;
    }

    /**
     * Retrieve HTML from a SubscriptionTracking object
     *
     * @return string
     */
    public function getHtml() {
        return $this->html;
    }

    /**
     * Add a substitution tag to a SubscriptionTracking object
     *
     * @param string $substitution_tag A tag that will be replaced with the
     *                                 unsubscribe URL. for example:
     *                                 [unsubscribe_url]. If this parameter
     *                                 is used, it will override both the text
     *                                 and html parameters. The URL of the link
     *                                 will be placed at the substitution tag’s
     *                                 location, with no additional formatting %>
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setSubstitutionTag($substitution_tag) {
        if (!is_string($substitution_tag)) {
            throw new CVendor_SendGrid_Mail_TypeException(
            '$substitution_tag must be of type string.'
            );
        }
        $this->substitution_tag = $substitution_tag;
    }

    /**
     * Retrieve a substitution tag from a SubscriptionTracking object
     *
     * @return string
     */
    public function getSubstitutionTag() {
        return $this->substitution_tag;
    }

    /**
     * Return an array representing a SubscriptionTracking object
     * for the Twilio SendGrid API
     *
     * @return null|array
     */
    public function jsonSerialize() {
        return array_filter(
                        [
                    'enable' => $this->getEnable(),
                    'text' => $this->getText(),
                    'html' => $this->getHtml(),
                    'substitution_tag' => $this->getSubstitutionTag()
                        ], function ($value) {
                    return $value !== null;
                }
                ) ?: null;
    }

}
