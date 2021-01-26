<?php

defined('SYSPATH') or die('No direct access allowed.');

//@codingStandardsIgnoreStart
/**
 * Undocumented class
 *
 * @deprecated 2.0
 */
class CPOP3 extends CObject {
    public function __construct() {
        $valid_prop = [
            'user',
            'password',
            'authhost',
        ];
        $this->add_valid_prop($valid_prop);
        $this->user = '';
        $this->password = '';
        $this->authhost = '';
    }

    public function set_user($user) {
        $this->user = $user;
        return $this;
    }

    public function set_password($password) {
        $this->password = $password;
        return $this;
    }

    public function set_authhost($authhost) {
        $this->authhost = $authhost;
        return $this;
    }

    public function set_host($host) {
        $auh = '';
        switch ($host) {
            case 'gmail':$auh = '{pop.gmail.com:995/pop3/ssl/novalidate-cert}';
                break;
            default: trigger_error('host not found in CPOP3');
                break;
        }
        $this->authhost = $auh;
        return $this;
    }

    public function login() {
        return new CPOP3_Connection($this->authhost, $this->user, $this->password);
    }

    public static function factory() {
        return new CPOP3();
    }
}
/**
 * @deprecated 2.0
 */
class CPOP3_Connection {
    private $_connection = null;

    public function __construct($authhost, $user, $pass) {
        $this->_connection = imap_open($authhost, $user, $pass);
    }

    public function __destruct() {
        @imap_close($this->_connection);
    }

    public function stat() {
        $check = imap_mailboxmsginfo($this->_connection);
        return ((array) $check);
    }

    public function get_last_message() {
        $num = $this->msg_count();
        //return $this->mail_mime_to_array(imap_body($this->_connection, $num),true);
        return $this->mail_mime_to_array($num, true);
    }

    public function msg_list($message = '') {
        if ($message) {
            $range = $message;
        } else {
            $MC = imap_check($this->_connection);
            $range = '1:' . $MC->Nmsgs;
        }
        $response = imap_fetch_overview($this->_connection, $range);
        foreach ($response as $msg) {
            $result[$msg->msgno] = (array) $msg;
        }
    }

    public function retrieve($message) {
        return(imap_fetchheader($this->_connection, $message, FT_PREFETCHTEXT));
    }

    public function delete($message) {
        return(imap_delete($this->_connection, $message));
    }

    public function msg_count() {
        return imap_num_msg($this->_connection);
    }

    public function mail_parse_headers($headers) {
        $headers = preg_replace('/\r\n\s+/m', '', $headers);
        preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)?\r\n/m', $headers, $matches);
        foreach ($matches[1] as $key => $value) {
            $result[$value] = $matches[2][$key];
        }
        return($result);
    }

    public function mail_mime_to_array($mid, $parse_headers = false) {
        $mail = imap_fetchstructure($this->_connection, $mid);
        $mail = $this->mail_get_parts($mid, $mail, 0);
        if ($parse_headers) {
            $mail[0]['parsed'] = $this->mail_parse_headers($mail[0]['data']);
        }
        return($mail);
    }

    public function mail_get_parts($mid, $part, $prefix) {
        $attachments = [];
        $attachments[$prefix] = $this->mail_decode_part($mid, $part, $prefix);
        if (isset($part->parts)) { // multipart
            $prefix = ($prefix == '0') ? '' : "$prefix.";
            foreach ($part->parts as $number => $subpart) {
                $attachments = array_merge($attachments, $this->mail_get_parts($mid, $subpart, $prefix . ($number + 1)));
            }
        }
        return $attachments;
    }

    public function mail_decode_part($message_number, $part, $prefix) {
        $attachment = [];

        if ($part->ifdparameters) {
            foreach ($part->dparameters as $object) {
                $attachment[strtolower($object->attribute)] = $object->value;
                if (strtolower($object->attribute) == 'filename') {
                    $attachment['is_attachment'] = true;
                    $attachment['filename'] = $object->value;
                }
            }
        }

        if ($part->ifparameters) {
            foreach ($part->parameters as $object) {
                $attachment[strtolower($object->attribute)] = $object->value;
                if (strtolower($object->attribute) == 'name') {
                    $attachment['is_attachment'] = true;
                    $attachment['name'] = $object->value;
                }
            }
        }

        $attachment['data'] = imap_fetchbody($this->_connection, $message_number, $prefix);
        if ($part->encoding == 3) { // 3 = BASE64
            $attachment['data'] = base64_decode($attachment['data']);
        } elseif ($part->encoding == 4) { // 4 = QUOTED-PRINTABLE
            $attachment['data'] = quoted_printable_decode($attachment['data']);
        }
        return($attachment);
    }
}

class CPOP3_Message {
    private $msg = [];
    private $parsed_header = [];

    public function construct($mail) {
        $this->msg = $mail;
        $this->parsed_header = $mail[0]['parsed'];
    }

    public function subject() {
        if (isset($this->parsed_header['Subject'])) {
            return $this->parsed_header['Subject'];
        }
        return false;
    }

    public function delivered_to() {
        if (isset($this->parsed_header['Delivered-To'])) {
            return $this->parsed_header['Delivered-To'];
        }
        return false;
    }

    public function received() {
        if (isset($this->parsed_header['Received'])) {
            return $this->parsed_header['Received'];
        }
        return false;
    }

    public function from() {
        if (isset($this->parsed_header['From'])) {
            return $this->parsed_header['From'];
        }
        return false;
    }

    public function to() {
        if (isset($this->parsed_header['To'])) {
            return $this->parsed_header['To'];
        }
        return false;
    }

    public function reply_to() {
        if (isset($this->parsed_header['Reply-To'])) {
            return $this->parsed_header['Reply-To'];
        }
        return false;
    }

    public function content_type() {
        if (isset($this->parsed_header['Content-Type'])) {
            return $this->parsed_header['Content-Type'];
        }
        return false;
    }

    public function received_date() {
        if (isset($this->parsed_header['Date'])) {
            return $this->parsed_header['Date'];
        }
        return false;
    }
}

//@codingStandardsIgnoreEnd
