<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2018, 4:19:33 AM
 */
class CApp_Message {
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

    public static function clearAll() {
        self::clear('error');
        self::clear('warning');
        self::clear('info');
        self::clear('success');
    }

    public static function flash($type) {
        $msgs = static::get($type);
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
        static::clear($type);
        if (strlen($message) > 0) {
            $alert = new CElement_Component_Alert();
            $alert->addClass('capp-message');
            $header = ucfirst($type) . '!';

            $message = $alert->setType($type)
                ->setTitle($header)
                ->setDismissable()->add($message)->html();
        }

        return $message;
    }

    public static function flashAll() {
        return static::flash('error') . static::flash('warning') . static::flash('info') . static::flash('success');
    }
}
