<?php
class CEmail_Client_Idn {
    public function encode($url) {
        return idn_to_ascii($url);
    }

    public function decode($url) {
        return idn_to_utf8($url);
    }
}
