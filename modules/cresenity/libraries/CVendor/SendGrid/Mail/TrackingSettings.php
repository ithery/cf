<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used to construct a TrackingSettings object for the
 * /mail/send API call
 *
 * @package SendGrid\Mail
 */
class TrackingSettings implements \JsonSerializable {

    /** @var $click_tracking CVendor_SendGrid_Mail_ClickTracking object */
    private $click_tracking;

    /** @var $open_tracking CVendor_SendGrid_Mail_OpenTracking object */
    private $open_tracking;

    /** @var $subscription_tracking CVendor_SendGrid_Mail_SubscriptionTracking object */
    private $subscription_tracking;

    /** @var $ganalytics CVendor_SendGrid_Mail_Ganalytics object */
    private $ganalytics;

    /**
     * Optional constructor
     *
     * @param CVendor_SendGrid_Mail_ClickTracking|null $click_tracking ClickTracking object
     * @param CVendor_SendGrid_Mail_OpenTracking|null $open_tracking OpenTracking object
     * @param CVendor_SendGrid_Mail_SubscriptionTracking|null $subscription_tracking SubscriptionTracking
     *                                                         object
     * @param CVendor_SendGrid_Mail_Ganalytics|null $ganalytics Ganalytics object
     */
    public function __construct(
    $click_tracking = null, $open_tracking = null, $subscription_tracking = null, $ganalytics = null
    ) {
        if (isset($click_tracking)) {
            $this->setClickTracking($click_tracking);
        }
        if (isset($open_tracking)) {
            $this->setOpenTracking($open_tracking);
        }
        if (isset($subscription_tracking)) {
            $this->setSubscriptionTracking($subscription_tracking);
        }
        if (isset($ganalytics)) {
            $this->setGanalytics($ganalytics);
        }
    }

    /**
     * Set the click tracking settings on a TrackingSettings object
     *
     * @param CVendor_SendGrid_Mail_ClickTracking|bool $enable The ClickTracking object or an
     *                                        indication if the setting is enabled
     * @param bool|null $enable_text Indicates if this setting should be
     *                                        included in the text/plain portion of
     *                                        your email
     */
    public function setClickTracking($enable, $enable_text = null) {
        if ($enable instanceof CVendor_SendGrid_Mail_ClickTracking) {
            $click_tracking = $enable;
            $this->click_tracking = $click_tracking;
            return;
        }
        $this->click_tracking = new CVendor_SendGrid_Mail_ClickTracking($enable, $enable_text);
    }

    /**
     * Retrieve the click tracking settings from a TrackingSettings object
     *
     * @return CVendor_SendGrid_Mail_ClickTracking
     */
    public function getClickTracking() {
        return $this->click_tracking;
    }

    /**
     * Set the open tracking settings on a TrackingSettings object
     *
     * @param CVendor_SendGrid_Mail_OpenTracking|bool $enable The ClickTracking object or an
     *                                            indication if the setting is
     *                                            enabled
     * @param string|null $substitution_tag Allows you to specify a
     *                                            substitution tag that you can
     *                                            insert in the body of your email
     *                                            at a location that you desire.
     *                                            This tag will be replaced by
     *                                            the open tracking pixelail
     *
     * @return null
     */
    public function setOpenTracking($enable, $substitution_tag = null) {
        if ($enable instanceof CVendor_SendGrid_Mail_OpenTracking) {
            $open_tracking = $enable;
            $this->open_tracking = $open_tracking;
            return;
        }
        $this->open_tracking = new CVendor_SendGrid_Mail_OpenTracking($enable, $substitution_tag);
        return;
    }

    /**
     * Retrieve the open tracking settings on a TrackingSettings object
     *
     * @return OpenTracking
     */
    public function getOpenTracking() {
        return $this->open_tracking;
    }

    /**
     * Set the subscription tracking settings on a TrackingSettings object
     *
     * @param CVendor_SendGrid_Mail_SubscriptionTracking|bool $enable The SubscriptionTracking
     *                                                    object or an indication
     *                                                    if the setting is enabled
     * @param string|null $text Text to be appended to the
     *                                                    email, with the
     *                                                    subscription tracking
     *                                                    link. You may control
     *                                                    where the link is by using
     *                                                    the tag <% %>
     * @param string|null $html HTML to be appended to the
     *                                                    email, with the
     *                                                    subscription tracking
     *                                                    link. You may control
     *                                                    where the link is by using
     *                                                    the tag <% %>
     * @param string|null $substitution_tag A tag that will be
     *                                                    replaced with the
     *                                                    unsubscribe URL. For
     *                                                    example:
     *                                                    [unsubscribe_url]. If this
     *                                                    parameter is used, it will
     *                                                    override both the text
     *                                                    and html parameters. The
     *                                                    URL of the link will be
     *                                                    placed at the substitution
     *                                                    tag’s location, with no
     *                                                    additional formatting
     */
    public function setSubscriptionTracking(
    $enable, $text = null, $html = null, $substitution_tag = null
    ) {
        if ($enable instanceof CVendor_SendGrid_Mail_SubscriptionTracking) {
            $subscription_tracking = $enable;
            $this->subscription_tracking = $subscription_tracking;
            return;
        }
        $this->subscription_tracking = new CVendor_SendGrid_Mail_SubscriptionTracking($enable, $text, $html, $substitution_tag);
    }

    /**
     * Retrieve the subscription tracking settings from a TrackingSettings object
     *
     * @return CVendor_SendGrid_Mail_SubscriptionTracking
     */
    public function getSubscriptionTracking() {
        return $this->subscription_tracking;
    }

    /**
     * Set the Google analytics settings on a TrackingSettings object
     *
     * @param CVendor_SendGrid_Mail_Ganalytics|bool $enable The Ganalytics object or an indication
     *                                      if the setting is enabled
     * @param string|null $utm_source Name of the referrer source. (e.g.
     *                                      Google, SomeDomain.com, or
     *                                      Marketing Email)
     * @param string|null $utm_medium Name of the marketing medium. (e.g.
     *                                      Email)
     * @param string|null $utm_term Used to identify any paid keywords
     * @param string|null $utm_content Used to differentiate your campaign from
     *                                      advertisements
     * @param string|null $utm_campaign The name of the campaign
     */
    public function setGanalytics(
    $enable, $utm_source = null, $utm_medium = null, $utm_term = null, $utm_content = null, $utm_campaign = null
    ) {
        if ($enable instanceof CVendor_SendGrid_Mail_Ganalytics) {
            $ganalytics = $enable;
            $this->ganalytics = $ganalytics;
            return;
        }
        $this->ganalytics = new CVendor_SendGrid_Mail_Ganalytics(
                $enable, $utm_source, $utm_medium, $utm_term, $utm_content, $utm_campaign
        );
    }

    /**
     * Retrieve the Google analytics settings from a TrackingSettings object
     *
     * @return Ganalytics
     */
    public function getGanalytics() {
        return $this->ganalytics;
    }

    /**
     * Return an array representing a TrackingSettings object for the Twilio SendGrid API
     *
     * @return null|array
     */
    public function jsonSerialize() {
        return array_filter(
                        [
                    'click_tracking' => $this->getClickTracking(),
                    'open_tracking' => $this->getOpenTracking(),
                    'subscription_tracking' => $this->getSubscriptionTracking(),
                    'ganalytics' => $this->getGanalytics()
                        ], function ($value) {
                    return $value !== null;
                }
                ) ?: null;
    }

}
