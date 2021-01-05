<?php

/**
 * Description of Session
 *
 * @author Hery
 */
trait CTrait_Compat_Session {

    // Flash variables
    protected static $flash;

    /**
     * Set a flash variable.
     *
     * @param   string|array  key, or array of values
     * @param   mixed         value (if keys is not an array)
     * @return  void
     */
    public function set_flash($keys, $val = FALSE) {
        if (empty($keys))
            return FALSE;

        if (!is_array($keys)) {
            $keys = array($keys => $val);
        }

        foreach ($keys as $key => $val) {
            if ($key == FALSE)
                continue;

            CSession::$flash[$key] = 'new';
            CSession::set($key, $val);
        }
    }

    /**
     * Freshen one, multiple or all flash variables.
     *
     * @param   string  variable key(s)
     * @return  void
     */
    public function keep_flash($keys = NULL) {
        $keys = ($keys === NULL) ? array_keys(CSession::$flash) : func_get_args();

        foreach ($keys as $key) {
            if (isset(CSession::$flash[$key])) {
                CSession::$flash[$key] = 'new';
            }
        }
    }

    /**
     * Expires old flash data and removes it from the session.
     *
     * @return  void
     */
    public function expire_flash() {
        static $run;

        // Method can only be run once
        if ($run === TRUE)
            return;

        if (!empty(CSession::$flash)) {
            foreach (CSession::$flash as $key => $state) {
                if ($state === 'old') {
                    // Flash has expired
                    unset(CSession::$flash[$key], $_SESSION[$key]);
                } else {
                    // Flash will expire
                    CSession::$flash[$key] = 'old';
                }
            }
        }

        // Method has been run
        $run = TRUE;
    }

}
