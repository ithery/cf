<?php

class CBot_Event_GenericEvent implements CBot_Contract_DriverEventInterface {
    protected $payload;

    protected $name;

    /**
     * @param $payload
     */
    public function __construct($payload) {
        $this->payload = $payload;
    }

    /**
     * Return the event name to match.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Return the event payload.
     *
     * @return mixed
     */
    public function getPayload() {
        return $this->payload;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }
}
