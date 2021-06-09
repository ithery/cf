<?php

/**
 * Description of SSH
 *
 * @author Hery
 */
class CDevSuite_Ssh {
    public $files;

    /**
     * Create a new Nginx instance.
     *
     * @return void
     */
    public function __construct() {
        $this->files = CDevSuite::filesystem();
    }

    /**
     * Get the configuration file path.
     *
     * @return string
     */
    public function path() {
        return CDevSuite::homePath() . '/ssh.json';
    }

    public function ensureFileExists() {
        if (!$this->files->exists($this->path())) {
            $this->write([]);
        }
    }

    public function create($name, $configuration) {
        if (!$this->isCanConnect($configuration)) {
            CDevSuite::info('Error when connecting to:' . $name . ', please check your configuration');
            return false;
        }

        $data = $this->read();
        $data[$name] = $configuration;

        $this->write($data);
        return true;
    }

    /**
     * Write the given configuration to disk.
     *
     * @param mixed $data
     *
     * @return void
     */
    public function write($data) {
        $this->files->putAsUser($this->path(), json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ) . PHP_EOL);
    }

    /**
     * Read the configuration file as JSON.
     *
     * @return array
     */
    public function read() {
        $this->ensureFileExists();
        return json_decode($this->files->get($this->path()), true);
    }

    public function getTableData() {
        $data = $this->read();

        return c::collect($data)->map(function ($item, $key) {
            return [
                'key' => $key,
                'host' => carr::get($item, 'host') . ':' . carr::get($item, 'port'),
                'type' => carr::get($item, 'passwordType'),
                'user' => carr::get($item, 'user'),
                'password' => carr::get($item, 'password'),
            ];
        });
    }

    public function exists($key) {
        return is_array(carr::get($this->read(), $key));
    }

    public function existsOrExit($key) {
        if (!$this->exists($key)) {
            CDevSuite::error('Databaes configuration: ' . $key . ' not exists');
            exit(CConsole::FAILURE_EXIT);
        }
    }

    public function isCanConnect($key) {
        try {
            $ssh = $this->getRemoteSsh($key);
            $output = $ssh->run('ls')->output();
        } catch (Exception $ex) {
            $errMessage = $ex->getMessage();
            CDevSuite::info($ex->getMessage());
            return false;
        }
        return true;
    }

    public function getRemoteSsh($key) {
        $config = $this->toRemoteSshConfig($key);
        return CRemote::ssh($config);
    }

    public function toRemoteSshConfig($keyFile) {
        $configArray = $keyFile;
        if (!is_array($configArray)) {
            $configArray = carr::get($this->read(), $keyFile);
        }

        $host = carr::get($configArray, 'host');
        $username = carr::get($configArray, 'user');
        $password = carr::get($configArray, 'password');
        $port = carr::get($configArray, 'port');
        $passwordType = carr::get($configArray, 'passwordType');

        $config = [
            'host' => $host,
            'name' => $host,
            'username' => $username,
            'port' => $port,
            'authentication_type' => $passwordType == 'password' ? 'prompt' : 'pubkey',
        ];
        if ($passwordType == 'password') {
            $config['password'] = $password;
        }
        if ($passwordType == 'pubkey') {
            $keytext = $this->files->get($password);
            $config['keytext'] = $keytext;
        }
        return $config;
    }
}
