<?php

/**
 * This class is used to construct a IpPoolName object for the /mail/send API call.
 */
class CVendor_SendGrid_Mail_IpPoolName implements \JsonSerializable {
    /**
     * @var string The IP Pool that you would like to send this email from. Minimum length: 2, Maximum Length: 64
     */
    private $ip_pool_name;

    /**
     * Optional constructor.
     *
     * @param null|string $ip_pool_name The IP Pool that you would like to
     *                                  send this email from. Minimum length:
     *                                  2, Maximum Length: 64
     */
    public function __construct($ip_pool_name = null) {
        if (isset($ip_pool_name)) {
            $this->setIpPoolName($ip_pool_name);
        }
    }

    /**
     * Set the ip pool name on a IpPoolName object.
     *
     * @param string $ip_pool_name The IP Pool that you would like to
     *                             send this email from. Minimum length:
     *                             2, Maximum Length: 64
     *
     * @throws CVendor_SendGrid_Exception_TypeException
     */
    public function setIpPoolName($ip_pool_name) {
        if (!is_string($ip_pool_name)) {
            throw new CVendor_SendGrid_Exception_TypeException('$ip_pool_name must be of type string.');
        }
        $this->ip_pool_name = $ip_pool_name;
    }

    /**
     * Retrieve the ip pool name from a IpPoolName object.
     *
     * @return string
     */
    public function getIpPoolName() {
        return $this->ip_pool_name;
    }

    /**
     * Return an array representing a IpPoolName object for the Twilio SendGrid API.
     *
     * @return string
     */
    public function jsonSerialize() {
        return $this->getIpPoolName();
    }
}
