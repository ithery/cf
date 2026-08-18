<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Message {
    /**
     * @param string $type
     * @param string $message
     *
     * @return void
     */
    public static function add($type, $message) {
        $session = CSession::store();
        if ($session == null) {
            return;
        }
        $msgs = $session->get('cmsg_' . $type);
        if (!is_array($msgs)) {
            $msgs = [];
        }
        $msgs[] = $message;
        $session->set('cmsg_' . $type, $msgs);
    }

    /**
     * @param string $type
     *
     * @return null|array|string
     */
    public static function get($type) {
        $session = CSession::store();
        if ($session == null) {
            return;
        }

        return $session->get('cmsg_' . $type);
    }

    /**
     * @param string $type
     *
     * @return void
     */
    public static function clear($type) {
        $session = CSession::store();
        if ($session == null) {
            return;
        }
        $session->set('cmsg_' . $type, null);
    }

    /**
     * @return void
     */
    public static function clearAll() {
        self::clear('error');
        self::clear('warning');
        self::clear('info');
        self::clear('success');
    }

    /**
     * @param string $type
     *
     * @return string
     */
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

    /**
     * @return string
     */
    public static function flashAll() {
        return static::flash('error') . static::flash('warning') . static::flash('info') . static::flash('success');
    }
}
