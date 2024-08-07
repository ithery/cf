<?php

/**
 * This class is used to construct a Ganalytics object for the /mail/send API call.
 */
class CVendor_SendGrid_Mail_Ganalytics implements \JsonSerializable {
    /**
     * @var bool Indicates if this setting is enabled
     */
    private $enable;

    /**
     * @var string Name of the referrer source. (e.g. Google, SomeDomain.com, or Marketing Email)
     */
    private $utm_source;

    /**
     * @var string Name of the marketing medium. (e.g. Email)
     */
    private $utm_medium;

    /**
     * @var string Used to identify any paid keywords
     */
    private $utm_term;

    /**
     * @var string Used to differentiate your campaign from advertisements
     */
    private $utm_content;

    /**
     * @var string The name of the campaign
     */
    private $utm_campaign;

    /**
     * Optional constructor.
     *
     * @param null|bool   $enable       Indicates if this setting is enabled
     * @param null|string $utm_source   Name of the referrer source. (e.g.
     *                                  Google, SomeDomain.com, or Marketing Email)
     * @param null|string $utm_medium   Name of the marketing medium. (e.g. Email)
     * @param null|string $utm_term     Used to identify any paid keywords
     * @param null|string $utm_content  Used to differentiate your campaign from
     *                                  advertisements
     * @param null|string $utm_campaign The name of the campaign
     */
    public function __construct(
        $enable = null,
        $utm_source = null,
        $utm_medium = null,
        $utm_term = null,
        $utm_content = null,
        $utm_campaign = null
    ) {
        if (isset($enable)) {
            $this->setEnable($enable);
        }
        if (isset($utm_source)) {
            $this->setCampaignSource($utm_source);
        }
        if (isset($utm_medium)) {
            $this->setCampaignMedium($utm_medium);
        }
        if (isset($utm_term)) {
            $this->setCampaignTerm($utm_term);
        }
        if (isset($utm_content)) {
            $this->setCampaignContent($utm_content);
        }
        if (isset($utm_campaign)) {
            $this->setCampaignName($utm_campaign);
        }
    }

    /**
     * Update the enable setting on a Ganalytics object.
     *
     * @param bool $enable Indicates if this setting is enabled
     *
     * @throws CVendor_SendGrid_Exception_TypeException
     */
    public function setEnable($enable) {
        if (!is_bool($enable)) {
            throw new CVendor_SendGrid_Exception_TypeException('$enable must be of type bool.');
        }
        $this->enable = $enable;
    }

    /**
     * Retrieve the enable setting on a Ganalytics object.
     *
     * @return bool
     */
    public function getEnable() {
        return $this->enable;
    }

    /**
     * Add the campaign source to a Ganalytics object.
     *
     * @param string $utm_source Name of the referrer source. (e.g.
     *                           Google, SomeDomain.com, or Marketing Email)
     *
     * @throws CVendor_SendGrid_Exception_TypeException
     */
    public function setCampaignSource($utm_source) {
        if (!is_string($utm_source)) {
            throw new CVendor_SendGrid_Exception_TypeException('$utm_source must be of type string.');
        }
        $this->utm_source = $utm_source;
    }

    /**
     * Return the campaign source from a Ganalytics object.
     *
     * @return string
     */
    public function getCampaignSource() {
        return $this->utm_source;
    }

    /**
     * Add the campaign medium to a Ganalytics object.
     *
     * @param string $utm_medium Name of the marketing medium. (e.g. Email)
     *
     * @throws CVendor_SendGrid_Exception_TypeException
     */
    public function setCampaignMedium($utm_medium) {
        if (!is_string($utm_medium)) {
            throw new CVendor_SendGrid_Exception_TypeException('$utm_medium must be of type string.');
        }
        $this->utm_medium = $utm_medium;
    }

    /**
     * Return the campaign medium from a Ganalytics object.
     *
     * @return string
     */
    public function getCampaignMedium() {
        return $this->utm_medium;
    }

    /**
     * Add the campaign term to a Ganalytics object.
     *
     * @param string $utm_term Used to identify any paid keywords
     *
     * @throws CVendor_SendGrid_Exception_TypeException
     */
    public function setCampaignTerm($utm_term) {
        if (!is_string($utm_term)) {
            throw new CVendor_SendGrid_Exception_TypeException('$utm_term must be of type string');
        }
        $this->utm_term = $utm_term;
    }

    /**
     * Return the campaign term from a Ganalytics object.
     *
     * @return string
     */
    public function getCampaignTerm() {
        return $this->utm_term;
    }

    /**
     * Add the campaign content to a Ganalytics object.
     *
     * @param string $utm_content Used to differentiate your campaign from
     *                            advertisements
     *
     * @throws CVendor_SendGrid_Exception_TypeException
     */
    public function setCampaignContent($utm_content) {
        if (!is_string($utm_content)) {
            throw new CVendor_SendGrid_Exception_TypeException('$utm_content must be of type string.');
        }
        $this->utm_content = $utm_content;
    }

    /**
     * Return the campaign content from a Ganalytics object.
     *
     * @return string
     */
    public function getCampaignContent() {
        return $this->utm_content;
    }

    /**
     * Add the campaign name to a Ganalytics object.
     *
     * @param string $utm_campaign The name of the campaign
     *
     * @throws CVendor_SendGrid_Exception_TypeException
     */
    public function setCampaignName($utm_campaign) {
        if (!is_string($utm_campaign)) {
            throw new CVendor_SendGrid_Exception_TypeException('$utm_campaign must be of type string.');
        }
        $this->utm_campaign = $utm_campaign;
    }

    /**
     * Return the campaign name from a Ganalytics object.
     *
     * @return string
     */
    public function getCampaignName() {
        return $this->utm_campaign;
    }

    /**
     * Return an array representing a Ganalytics object for the Twilio SendGrid API.
     *
     * @return null|array
     */
    public function jsonSerialize() {
        return array_filter(
            [
                'enable' => $this->getEnable(),
                'utm_source' => $this->getCampaignSource(),
                'utm_medium' => $this->getCampaignMedium(),
                'utm_term' => $this->getCampaignTerm(),
                'utm_content' => $this->getCampaignContent(),
                'utm_campaign' => $this->getCampaignName()
            ],
            function ($value) {
                return $value !== null;
            }
        ) ?: null;
    }
}
