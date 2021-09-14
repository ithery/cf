<?php

interface CEmail_DriverInterface {
    public function send(array $to, $subject, $body, $options = []);
}
