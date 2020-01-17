<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used to construct a Asm object for the /mail/send API call
 *
 * @package SendGrid\Mail
 */
class CVendor_SendGrid_Mail_Asm implements \JsonSerializable {

    /** @var $group_id int The unsubscribe group to associate with this email */
    private $group_id;

    /**
     * @var $groups_to_display int[] An array containing the unsubscribe groups that you
     * would like to be displayed on the unsubscribe preferences page.
     */
    private $groups_to_display;

    /**
     * Optional constructor
     *
     * @param int|CVendor_SendGrid_Mail_GroupId|null $group_id A GroupId object or the
     *                                                      unsubscribe group to
     *                                                      associate with this email
     * @param int[]|CVendor_SendGrid_Mail_GroupsToDisplay|null $groups_to_display A GroupsToDisplay
     *                                                      object or an array
     *                                                      containing the
     *                                                      unsubscribe groups
     *                                                      that you would like
     *                                                      to be displayed
     *                                                      on the unsubscribe
     *                                                      preferences page.
     */
    public function __construct(
    $group_id = null, $groups_to_display = null
    ) {
        if (isset($group_id)) {
            $this->setGroupId($group_id);
        }
        if (isset($groups_to_display)) {
            $this->setGroupsToDisplay($groups_to_display);
        }
    }

    /**
     * Add the group id to a Asm object
     *
     * @param int|CVendor_SendGrid_Mail_GroupId $group_id The unsubscribe group to associate with this
     *                              email
     *
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setGroupId($group_id) {
        if ($group_id instanceof CVendor_SendGrid_Mail_GroupId) {
            $this->group_id = $group_id->getGroupId();
        } else {
            if (!is_int($group_id)) {
                throw new CVendor_SendGrid_Mail_TypeException(
                '$group_id must be an instance of Twilio SendGrid\Mail\GroupId or of type int.'
                );
            }
            $this->group_id = new CVendor_SendGrid_Mail_GroupId($group_id);
        }
        return;
    }

    /**
     * Retrieve the GroupId object from a Asm object
     *
     * The unsubscribe group to associate with this email
     *
     * @return int
     */
    public function getGroupId() {
        return $this->group_id;
    }

    /**
     * Add the groups to display id(s) to a Asm object
     *
     * @param int[]|CVendor_SendGrid_Mail_GroupsToDisplay $groups_to_display A GroupsToDisplay
     *                                                 object or an array
     *                                                 containing the
     *                                                 unsubscribe groups
     *                                                 that you would like
     *                                                 to be displayed
     *                                                 on the unsubscribe
     *                                                 preferences page.
     *
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setGroupsToDisplay($groups_to_display) {
        if ($groups_to_display instanceof CVendor_SendGrid_Mail_GroupsToDisplay) {
            $this->groups_to_display = $groups_to_display->getGroupsToDisplay();
        } else {
            if (!is_array($groups_to_display)) {
                throw new CVendor_SendGrid_Mail_TypeException(
                '$groups_to_display must be an instance of Twilio SendGrid\Mail\GroupsToDisplay or of type array.'
                );
            }
            $this->groups_to_display = new CVendor_SendGrid_Mail_GroupsToDisplay($groups_to_display);
        }
        return;
    }

    /**
     * Retrieve the groups to display id(s) from a Asm object
     *
     * @return int[]
     */
    public function getGroupsToDisplay() {
        return $this->groups_to_display;
    }

    /**
     * Return an array representing a Asm object for the Twilio SendGrid API
     *
     * @return null|array
     */
    public function jsonSerialize() {
        return array_filter(
                        [
                    'group_id' => $this->getGroupId(),
                    'groups_to_display' => $this->getGroupsToDisplay()
                        ], function ($value) {
                    return $value !== null;
                }
                ) ?: null;
    }

}
