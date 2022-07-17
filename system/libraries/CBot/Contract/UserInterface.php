<?php

interface CBot_Contract_UserInterface {
    /**
     * @return string
     */
    public function getId();

    /**
     * @return null|string
     */
    public function getUsername();

    /**
     * @return null|string
     */
    public function getFirstName();

    /**
     * @return null|string
     */
    public function getLastName();

    /**
     * Get raw driver's user info.
     *
     * @return array
     */
    public function getInfo();
}
