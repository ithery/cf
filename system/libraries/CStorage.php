<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 3:29:43 AM
 */
use Aws\S3\S3Client;
use League\Flysystem\Visibility;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter as S3Adapter;
use League\Flysystem\FilesystemAdapter as FlysystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter as AwsS3PortableVisibilityConverter;

class CStorage {
    protected static $instance;

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
     * @return CStorage
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

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
     * @param null|string $name
     *
     * @return CStorage_FilesystemInterface
     */
    public function drive($name = null) {
        return $this->disk($name);
    }

    /**
     * Get a filesystem instance.
     *
     * @param null|string $name
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
     * @param null|string $name
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
     * Build an on-demand disk.
     *
     * @param string|array $config
     *
     * @return \CStorage_FilesystemInterface
     */
    public function build($config) {
        return $this->resolve('ondemand', is_array($config) ? $config : [
            'driver' => 'local',
            'root' => $config,
        ]);
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
     * @param string $name
     *
     * @return string
     */
    protected function sanitizeDriverName($name) {
        if ($name == 'bunnycdn') {
            $name = 'bunnyCDN';
        }

        return ucfirst($name);
    }

    /**
     * Resolve the given disk.
     *
     * @param string     $name
     * @param null|mixed $config
     *
     * @throws \InvalidArgumentException
     *
     * @return CStorage_FilesystemInterface
     */
    protected function resolve($name, $config = null) {
        if ($config == null) {
            $config = $this->getConfig($name);
        }
        if (empty($config['driver'])) {
            throw new InvalidArgumentException("Disk [{$name}] does not have a configured driver.");
        }
        $name = $config['driver'];
        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }

        $driverMethod = 'create' . $this->sanitizeDriverName($config['driver']) . 'Driver';
        if (!method_exists($this, $driverMethod)) {
            throw new InvalidArgumentException("Driver [{$name}] is not supported.");
        }

        return $this->{$driverMethod}($config);
    }

    /**
     * Call a custom driver creator.
     *
     * @param array $config
     *
     * @return CStorage_FilesystemInterface
     */
    protected function callCustomCreator(array $config) {
        $driver = $this->customCreators[$config['driver']]($config);

        return $driver;
    }

    /**
     * Create an instance of the local driver.
     *
     * @param array $config
     *
     * @return CStorage_FilesystemInterface
     */
    public function createLocalDriver(array $config) {
        $visibility = PortableVisibilityConverter::fromArray(
            carr::get($config, 'permissions', []),
            carr::get($config, 'directory_visibility', carr::get($config, 'visibility', Visibility::VISIBILITY_PUBLIC))
        );

        $links = (isset($config['links']) ? $config['links'] : null) === 'skip' ? LocalAdapter::SKIP_LINKS : LocalAdapter::DISALLOW_LINKS;
        $adapter = new LocalAdapter(
            $config['root'],
            $visibility,
            carr::get($config, 'lock', LOCK_EX),
            $links
        );

        return new CStorage_Adapter($this->createFlysystem($adapter, $config), $adapter, $config);
    }

    /**
     * Create an instance of the ftp driver.
     *
     * @param array $config
     *
     * @return \CStorage_FilesystemInterface
     */
    public function createBunnyCDNDriver(array $config) {
        $adapter = new CStorage_Adapter_BunnyCDNAdapter(
            new CStorage_Vendor_BunnyCDN_Client(
                $config['storage_zone'],
                $config['api_key'],
                $config['region']
            ),
            isset($config['endpoint']) ? $config['endpoint'] : null
        );

        // return new FilesystemAdapter(
        //     new Filesystem($adapter, $config),
        //     $adapter,
        //     $config
        // );

        return new CStorage_Adapter($this->createFlysystem($adapter, $config), $adapter, $config);
    }

    /**
     * Create an instance of the ftp driver.
     *
     * @param array $config
     *
     * @return \CStorage_FilesystemInterface
     */
    public function createFtpDriver(array $config) {
        if (!isset($config['root'])) {
            $config['root'] = '';
        }

        $adapter = new FtpAdapter(FtpConnectionOptions::fromArray($config));

        return new CStorage_Adapter($this->createFlysystem($adapter, $config), $adapter, $config);
    }

    /**
     * Create an instance of the sftp driver.
     *
     * @param array $config
     *
     * @return \CStorage_FilesystemInterface
     */
    public function createSftpDriver(array $config) {
        $provider = SftpConnectionProvider::fromArray($config);

        $root = carr::get($config, 'root', '/');

        $visibility = PortableVisibilityConverter::fromArray(
            carr::get($config, 'permissions', [])
        );

        $adapter = new SftpAdapter($provider, $root, $visibility);

        return new CStorage_Adapter($this->createFlysystem($adapter, $config), $adapter, $config);
    }

    /**
     * Create an instance of the Amazon S3 driver.
     *
     * @param array $config
     *
     * @return \CStorage_CloudInterface
     */
    public function createS3Driver(array $config) {
        $s3Config = $this->formatS3Config($config);

        $root = (string) carr::get($s3Config, 'root', '');

        $visibility = new AwsS3PortableVisibilityConverter(
            isset($config['visibility']) ? $config['visibility'] : Visibility::VISIBILITY_PRIVATE
        );

        $streamReads = carr::get($s3Config, 'stream_reads', false);

        $client = new S3Client($s3Config);

        $adapter = new S3Adapter($client, $s3Config['bucket'], $root, $visibility, null, carr::get($config, 'options', []), $streamReads);

        return new CStorage_Adapter_AwsS3V3Adapter(
            $this->createFlysystem($adapter, $config),
            $adapter,
            $s3Config,
            $client
        );
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
     * @param \League\Flysystem\FilesystemAdapter $adapter
     * @param array                               $config
     *
     * @return \League\Flysystem\FilesystemOperator
     */
    protected function createFlysystem(FlysystemAdapter $adapter, array $config) {
        return new Flysystem($adapter, carr::only($config, [
            'directory_visibility',
            'disable_asserts',
            'temporary_url',
            'url',
            'visibility',
        ]));
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
     * Disconnect the given disk and remove from local cache.
     *
     * @param null|string $name
     *
     * @return void
     */
    public function purge($name = null) {
        if ($name == null) {
            $name = $this->getDefaultDriver();
        }

        unset($this->disks[$name]);
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
