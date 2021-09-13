<?php

defined('SYSPATH') or die('No direct access allowed.');
//@codingStandardsIgnoreStart
/**
 * @deprecated since 1.2
*/
class cmsg {
    public static function add($type, $message) {
        $session = CSession::instance();
        $msgs = $session->get('cmsg_' . $type);
        if (!is_array($msgs)) {
            $msgs = [];
        }
        $msgs[] = $message;
        $session->set('cmsg_' . $type, $msgs);
    }

    public static function get($type) {
        $session = CSession::instance();
        return $session->get('cmsg_' . $type);
    }

    public static function clear($type) {
        $session = CSession::instance();
        $session->set('cmsg_' . $type, null);
    }

    public static function clear_all() {
        self::clear('error');
        self::clear('warning');
        self::clear('info');
        self::clear('success');
    }

    public static function flash($type) {
        $msgs = cmsg::get($type);
        $message = '';
        if (is_array($msgs)) {
            foreach ($msgs as $msg) {
                $message .= '<p>' . $msg . '</p>';
            }
        } elseif (is_string($msgs)) {
            if (strlen($msgs) > 0) {
                $message = $msgs;
            }
        }
        cmsg::clear($type);
        if (strlen($message) > 0) {
            $alert = new CElement_Component_Alert();
            $header = ucfirst($type) . '!';

            $message = $alert->setType($type)
                ->setTitle($header)
                ->setDismissable()->add($message)->html();
        }
        return $message;
    }

    public static function flash_all() {
        return cmsg::flash('error') . cmsg::flash('warning') . cmsg::flash('info') . cmsg::flash('success');
    }
}
 //@codingStandardsIgnoreEnd
