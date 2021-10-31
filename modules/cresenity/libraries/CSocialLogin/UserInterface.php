<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 15, 2019, 9:09:35 PM
 */
interface CSocialLogin_UserInterface {
    /**
     * Get the unique identifier for the user.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the nickname / username for the user.
     *
     * @return string
     */
    public function getNickname();

    /**
     * Get the full name of the user.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the e-mail address of the user.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Get the avatar / image URL for the user.
     *
     * @return string
     */
    public function getAvatar();
}
