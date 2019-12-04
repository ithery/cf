<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Session database driver.
 */
class CSession_Driver_Database implements CSession_Driver {
    /*
      CREATE TABLE sessions
      (
      session_id VARCHAR(127) NOT NULL,
      last_activity INT(10) UNSIGNED NOT NULL,
      data TEXT NOT NULL,
      PRIMARY KEY (session_id)
      );
     */

    // Database settings
    protected $db = 'default';
    protected $table = 'session';
    // Encryption
    protected $encrypt;
    // Session settings
    protected $session_id;
    protected $written = FALSE;

    public function __construct() {
        // Load configuration
        $config = CF::config('session');

        if (!empty($config['encryption'])) {
            // Load encryption
            $this->encrypt = Encrypt::instance();
        }

        if (isset($config['storage'])) {
            // $domain = CF::domain();
            // $file = CF::get_file('config', 'database', $domain);
            // $allConfig = include $file;
            // $dbConfig = $allConfig[$this->db];
            // carr::set_path($dbConfig, 'connection.database', $config['storage']);
            // $this->db = $dbConfig;

            $this->db = $config['storage'];
        }

        // if (is_array($config['storage'])) {
        //     if (!empty($config['storage']['group'])) {
        //         // Set the group name
        //         $this->db = $config['storage']['group'];
        //     }
        //     if (!empty($config['storage']['table'])) {
        //         // Set the table name
        //         $this->table = $config['storage']['table'];
        //     }
        // }
        // Load database
        $this->db = CDatabase::instance($this->db, null, null);

        CF::log(CLogger::DEBUG, 'Session Database Driver Initialized');
    }

    public function open($path, $name) {
        return TRUE;
    }

    public function close() {
        return TRUE;
    }

    public function read($id) {
        // Load the session
        $query = $this->db->from($this->table)->where('key', $id)->limit(1)->get()->result(TRUE);

        if ($query->count() === 0) {
            // No current session
            $this->session_id = NULL;

            return '';
        }

        // Set the current session id
        $this->session_id = $id;

        // Load the data
        $data = $query->current()->value;

        return ($this->encrypt === NULL) ? base64_decode($data) : $this->encrypt->decode($data);
    }

    public function write($id, $data) {
        $data = array(
            'key' => $id,
            'value' => ($this->encrypt === NULL) ? base64_encode($data) : $this->encrypt->encode($data),
            'updated' => date('Y-m-d H:i:s'),
        );

        if ($this->session_id === NULL) {
            // Insert a new session
            $data['created'] = date('Y-m-d H:i:s');
            $query = $this->db->insert($this->table, $data);
        } elseif ($id === $this->session_id) {
            // Do not update the session_id
            unset($data['session_id']);

            // Update the existing session
            $query = $this->db->update($this->table, $data, array('key' => $id));
        } else {
            // Update the session and id
            $query = $this->db->update($this->table, $data, array('key' => $this->session_id));

            // Set the new session id
            $this->session_id = $id;
        }

        return (bool) $query->count();
    }

    public function destroy($id) {
        // Delete the requested session
        $this->db->delete($this->table, array('key' => $id));

        // Session id is no longer valid
        $this->session_id = NULL;

        return TRUE;
    }

    public function regenerate() {
        // Generate a new session id
        session_regenerate_id();

        // Return new session id
        return session_id();
    }

    public function gc($maxlifetime) {
        // Delete all expired sessions
        $query = $this->db->delete($this->table, array('created <' => time() - $maxlifetime));

        CF::log(CLogger::DEBUG, 'Session garbage collected: ' . $query->count() . ' row(s) deleted.');

        return TRUE;
    }

}

// End Session Database Driver
