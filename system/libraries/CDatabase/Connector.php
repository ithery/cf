<?php
use Doctrine\DBAL\Driver\PDOConnection;

abstract class CDatabase_Connector implements CDatabase_ConnectorInterface {
    use CDatabase_Trait_DetectLostConnection;

    /**
     * The default PDO connection options.
     *
     * @var array
     */
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * Create a new PDO connection.
     *
     * @param string $dsn
     * @param array  $config
     * @param array  $options
     *
     * @throws \Exception
     *
     * @return \PDO
     */
    public function createConnection($dsn, array $config, array $options) {
        $username = carr::get($config, 'username');
        $password = carr::get($config, 'password');

        try {
            return $this->createPdoConnection(
                $dsn,
                $username,
                $password,
                $options
            );
        } catch (Exception $e) {
            return $this->tryAgainIfCausedByLostConnection(
                $e,
                $dsn,
                $username,
                $password,
                $options
            );
        }
    }

    /**
     * Create a new PDO connection instance.
     *
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array  $options
     *
     * @return \PDO
     */
    protected function createPdoConnection($dsn, $username, $password, $options) {
        return new PDO($dsn, $username, $password, $options);
    }

    /**
     * Determine if the connection is persistent.
     *
     * @param array $options
     *
     * @return bool
     */
    protected function isPersistentConnection($options) {
        return isset($options[PDO::ATTR_PERSISTENT])
               && $options[PDO::ATTR_PERSISTENT];
    }

    /**
     * Handle an exception that occurred during connect execution.
     *
     * @param \Throwable $e
     * @param string     $dsn
     * @param string     $username
     * @param string     $password
     * @param array      $options
     *
     * @throws \Exception
     *
     * @return \PDO
     */
    protected function tryAgainIfCausedByLostConnection($e, $dsn, $username, $password, $options) {
        if ($this->causedByLostConnection($e)) {
            return $this->createPdoConnection($dsn, $username, $password, $options);
        }

        throw $e;
    }

    /**
     * Get the PDO options based on the configuration.
     *
     * @param array $config
     *
     * @return array
     */
    public function getOptions(array $config) {
        $options = carr::get($config, 'options', []);

        return array_diff_key($this->options, $options) + $options;
    }

    /**
     * Get the default PDO connection options.
     *
     * @return array
     */
    public function getDefaultOptions() {
        return $this->options;
    }

    /**
     * Set the default PDO connection options.
     *
     * @param array $options
     *
     * @return void
     */
    public function setDefaultOptions(array $options) {
        $this->options = $options;
    }
}
