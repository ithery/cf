<?php

class CEmail_Driver_NullDriver extends CEmail_DriverAbstract {
    public function send(array $to, $subject, $body, $options = []) {
        return null;
    }
}
