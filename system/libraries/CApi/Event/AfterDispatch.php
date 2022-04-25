<?php

/**
 * Description of RequestHandled.
 *
 * @author Hery
 */
class CApi_Event_AfterDispatch {
    /**
     * The method response instance.
     *
     * @var CApi_MethodResponse
     */
    public $methodResponse;

    /**
     * Create a new event instance.
     *
     * @param CApi_MethodResponse $methodResponse
     *
     * @return void
     */
    public function __construct($methodResponse) {
        $this->methodResponse = $methodResponse;
    }
}
