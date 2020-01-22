<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used to construct a GroupsToDisplay object for
 * the /mail/send API call
 *
 * @package SendGrid\Mail
 */
class CVendor_SendGrid_Mail_GroupsToDisplay implements \JsonSerializable {

    /** @var $groups_to_display int[] An array containing the unsubscribe groups that you would like to be displayed on the unsubscribe preferences page. Maximum of 25 */
    private $groups_to_display;

    /**
     * Optional constructor
     *
     * @param int[]|int|null $groups_to_display An array containing
     *                                          the unsubscribe groups
     *                                          that you would like to
     *                                          be displayed on the
     *                                          unsubscribe preferences
     *                                          page. Maximum of 25
     */
    public function __construct($groups_to_display = null) {
        if (isset($groups_to_display)) {
            $this->setGroupsToDisplay($groups_to_display);
        }
    }

    /**
     * Add a group to display on a GroupsToDisplay object
     *
     * @param int|int[] $groups_to_display The unsubscribe group(s)
     *                                     that you would like to be
     *                                     displayed on the unsubscribe
     *                                     preferences page
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     * @return null
     */
    public function setGroupsToDisplay($groups_to_display) {
        if (!is_array($groups_to_display)) {
            throw new CVendor_SendGrid_Mail_TypeException('$groups_to_display must be an array.');
        }
        if (is_array($groups_to_display)) {
            $this->groups_to_display = $groups_to_display;
        } else {
            $this->groups_to_display[] = $groups_to_display;
        }
    }

    /**
     * Return the group(s) to display on a GroupsToDisplay object
     *
     * @return int[]
     */
    public function getGroupsToDisplay() {
        return $this->groups_to_display;
    }

    /**
     * Return an array representing a GroupsToDisplay object for the Twilio SendGrid API
     *
     * @return null|array
     */
    public function jsonSerialize() {
        return $this->getGroupsToDisplay();
    }

}
