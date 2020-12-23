<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 3:29:43 AM
 */
use Aws\S3\S3Client;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Sftp\SftpAdapter;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Adapter\Ftp as FtpAdapter;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\AwsS3v3\AwsS3Adapter as S3Adapter;
use League\Flysystem\Cached\Storage\Memory as MemoryStore;

class CStorage {
    protected static $instance;

    /**
     * @return CStorage
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * The array of resolved filesystem drivers.
     *
     * @var array
     */
    protected $disks = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * Create a new filesystem manager instance.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Get a filesystem instance.
     *
     * @param string|null $name
     *
     * @return CStorage_FilesystemInterface
     */
    public function drive($name = null) {
        return $this->disk($name);
    }

    /**
     * Get a filesystem instance.
     *
     * @param string|null $name
     *
     * @return CStorage_Adapter
     */
    public function disk($name = null) {
        $name = $name ?: $this->getDefaultDriver();
        return $this->disks[$name] = $this->get($name);
    }

    /**
     * Get a filesystem instance.
     *
     * @param string|null $name
     *
     * @return CStorage_FilesystemInterface
     */
    public function temp($name = null) {
        $name = $name ?: $this->getTempDriver();
        return $this->disks[$name] = $this->get($name);
    }

    /**
     * Get a default cloud filesystem instance.
     *
     * @return CStorage_FilesystemInterface
     */
    public function cloud() {
        $name = $this->getDefaultCloudDriver();
        return $this->disks[$name] = $this->get($name);
    }

    /**
     * Attempt to get the disk from the local cache.
     *
     * @param string $name
     *
     * @return CStorage_FilesystemInterface
     */
    protected function get($name) {
        return isset($this->disks[$name]) ? $this->disks[$name] : $this->resolve($name);
    }

    /**
     * Resolve the given disk.
     *
     * @param string $name
     *
     * @return CStorage_FilesystemInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name) {
        $config = $this->getConfig($name);
        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }
        $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';
        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
        }
    }

    /**
     * Call a custom driver creator.
     *
     * @param array $config
     *
     * @return CStorage_FilesystemInterface
     */
    protected function callCustomCreator(array $config) {
        $driver = $this->customCreators[$config['driver']]($this->app, $config);
        if ($driver instanceof FilesystemInterface) {
            return $this->adapt($driver);
        }
        return $driver;
    }

    /**
     * Create an instance of the google drive driver.
     *
     * @param array $config
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function createGoogleDriver(array $config) {
        $client = new \Google_Client;
        $client->setClientId(carr::get($config, 'clientId'));
        $client->setClientSecret(carr::get($config, 'clientSecret'));
        $client->refreshToken(carr::get($config, 'refreshToken'));
        $folderId = carr::get($config, 'folderId');
        $service = new \Google_Service_Drive($client);
        return $this->adapt($this->createFlysystem(
            new CStorage_Adapter_GoogleDriveAdapter($service, $folderId),
            $config
        ));
    }

    /**
     * Create an instance of the local driver.
     *
     * @param array $config
     *
     * @return CStorage_FilesystemInterface
     */
    public function createLocalDriver(array $config) {
        $permissions = isset($config['permissions']) ? $config['permissions'] : [];
        $links = (isset($config['links']) ? $config['links'] : null) === 'skip' ? LocalAdapter::SKIP_LINKS : LocalAdapter::DISALLOW_LINKS;
        return $this->adapt($this->createFlysystem(new LocalAdapter(
            $config['root'],
            isset($config['lock']) ? $config['lock'] : LOCK_EX,
            $links,
            $permissions
        ), $config));
    }

    /**
     * Create an instance of the ftp driver.
     *
     * @param array $config
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function createFtpDriver(array $config) {
        return $this->adapt($this->createFlysystem(
            new FtpAdapter($config),
            $config
        ));
    }

    /**
     * Create an instance of the sftp driver.
     *
     * @param array $config
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function createSftpDriver(array $config) {
        return $this->adapt($this->createFlysystem(
            new SftpAdapter($config),
            $config
        ));
    }

    /**
     * Create an instance of the Amazon S3 driver.
     *
     * @param array $config
     *
     * @return \Illuminate\Contracts\Filesystem\Cloud
     */
    public function createS3Driver(array $config) {
        $s3Config = $this->formatS3Config($config);
        $root = isset($s3Config['root']) ? $s3Config['root'] : null;
        $options = isset($config['options']) ? $config['options'] : [];

        return $this->adapt($this->createFlysystem(
            new S3Adapter(new S3Client($s3Config), $s3Config['bucket'], $root, $options),
            $config
        ));
    }

    /**
     * Format the given S3 configuration with the default options.
     *
     * @param array $config
     *
     * @return array
     */
    protected function formatS3Config(array $config) {
        $config += ['version' => 'latest'];
        if ($config['key'] && $config['secret']) {
            $config['credentials'] = carr::only($config, ['key', 'secret', 'token']);
        }

        return $config;
    }

    /**
     * Create a Flysystem instance with the given adapter.
     *
     * @param \League\Flysystem\AdapterInterface $adapter
     * @param array                              $config
     *
     * @return \League\Flysystem\FilesystemInterface
     */
    protected function createFlysystem(AdapterInterface $adapter, array $config) {
        $cache = carr::pull($config, 'cache');
        $config = carr::only($config, ['visibility', 'disable_asserts', 'url']);
        if ($cache) {
            $adapter = new CachedAdapter($adapter, $this->createCacheStore($cache));
        }
        return new Flysystem($adapter, count($config) > 0 ? $config : null);
    }

    /**
     * Create a cache store instance.
     *
     * @param mixed $config
     *
     * @return \League\Flysystem\Cached\CacheInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function createCacheStore($config) {
        if ($config === true) {
            return new MemoryStore;
        }
        return new Cache(
            $this->app['cache']->store($config['store']),
            isset($config['prefix']) ? $config['prefix'] : 'flysystem',
            isset($config['expire']) ? $config['expire'] : null
        );
    }

    /**
     * Adapt the filesystem implementation.
     *
     * @param \League\Flysystem\FilesystemInterface $filesystem
     *
     * @return CStorage_FilesystemInterface
     */
    protected function adapt(FilesystemInterface $filesystem) {
        return new CStorage_Adapter($filesystem);
    }

    /**
     * Set the given disk instance.
     *
     * @param string $name
     * @param mixed  $disk
     *
     * @return $this
     */
    public function set($name, $disk) {
        $this->disks[$name] = $disk;
        return $this;
    }

    /**
     * Get the filesystem connection configuration.
     *
     * @param string $name
     *
     * @return array
     */
    protected function getConfig($name) {
        return CF::config("storage.disks.{$name}");
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver() {
        return CF::config('storage.default');
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getTempDriver() {
        return CF::config('storage.temp');
    }

    /**
     * Get the default cloud driver name.
     *
     * @return string
     */
    public function getDefaultCloudDriver() {
        return CF::config('storage.cloud');
    }

    /**
     * Unset the given disk instances.
     *
     * @param array|string $disk
     *
     * @return $this
     */
    public function forgetDisk($disk) {
        foreach ((array) $disk as $diskName) {
            unset($this->disks[$diskName]);
        }
        return $this;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param string   $driver
     * @param \Closure $callback
     *
     * @return $this
     */
    public function extend($driver, Closure $callback) {
        $this->customCreators[$driver] = $callback;
        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->disk()->$method(...$parameters);
    }
}
