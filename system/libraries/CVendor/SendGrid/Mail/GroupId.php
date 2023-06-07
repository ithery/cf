<?php

/**
 * This class is used to construct a GroupId object for the /mail/send API call.
 */
class CVendor_SendGrid_Mail_GroupId implements \JsonSerializable {
    /**
     * @var int The unsubscribe group to associate with this email
     */
    private $group_id;

    /**
     * Optional constructor.
     *
     * @param null|int $group_id The unsubscribe group to associate with this email
     */
    public function __construct($group_id = null) {
        if (isset($group_id)) {
            $this->setGroupId($group_id);
        }
    }

    /**
     * Add the group id to a GroupId object.
     *
     * @param int $group_id The unsubscribe group to associate with this email
     *
     * @throws CVendor_SendGrid_Exception_TypeException
     */
    public function setGroupId($group_id) {
        if (!is_int($group_id)) {
            throw new CVendor_SendGrid_Exception_TypeException('$group_id must be of type int.');
        }
        $this->group_id = $group_id;
    }

    /**
     * Retrieve the group id from a GroupId object.
     *
     * @return int
     */
    public function getGroupId() {
        return $this->group_id;
    }

    /**
     * Return an array representing a GroupId object for the Twilio SendGrid API.
     *
     * @return int
     */
    public function jsonSerialize() {
        return $this->getGroupId();
    }
}
