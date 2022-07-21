<?php
class CServer_SMTP_MessageFactory {
    /**
     * Create a new message instance from the given zend message.
     *
     * @param string $content
     * @param string $from
     * @param array  $to
     *
     * @return CServer_SMTP_Message
     */
    public static function make(string $content, string $from, array $to) {
        return new CServer_SMTP_Message($content, $from, $to);
    }
}
