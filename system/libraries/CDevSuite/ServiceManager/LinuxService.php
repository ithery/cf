<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite_ServiceManager_LinuxService extends CDevSuite_ServiceManager {

    public $cli;

    /**
     * Create a new Brew instance.
     *
     * @param CommandLine $cli CommandLine object
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
    }

    /**
     * Start the given services.
     *
     * @param mixed $services Service name
     *
     * @return void
     */
    public function start($services) {
        $services = is_array($services) ? $services : func_get_args();

        foreach ($services as $service) {
            CDevSuite::info("Starting $service...");
            $this->cli->quietly('sudo service ' . $this->getRealService($service) . ' start');
        }
    }

    /**
     * Stop the given services.
     *
     * @param mixed $services Service name
     *
     * @return void
     */
    public function stop($services) {
        $services = is_array($services) ? $services : func_get_args();

        foreach ($services as $service) {
            CDevSuite::info("Stopping $service...");
            $this->cli->quietly('sudo service ' . $this->getRealService($service) . ' stop');
        }
    }

    /**
     * Restart the given services.
     *
     * @param mixed $services Service name
     *
     * @return void
     */
    public function restart($services) {
        $services = is_array($services) ? $services : func_get_args();

        foreach ($services as $service) {
            CDevSuite::info("Restarting $service...");
            $this->cli->quietly('sudo service ' . $this->getRealService($service) . ' restart');
        }
    }

    /**
     * Status of the given services.
     *
     * @param mixed $services Service name
     *
     * @return void
     */
    public function printStatus($services) {
        $services = is_array($services) ? $services : func_get_args();

        foreach ($services as $service) {
            if ($this->_hasSystemd()) {
                $status = $this->cli->run(
                        'systemctl status ' . $this->getRealService($service) . ' | grep "Active:"'
                );

                $running = strpos(trim($status), 'running');

                if ($running) {
                    return CDevSuite::info(ucfirst($service) . ' is running...');
                } else {
                    return CDevSuite::warning(ucfirst($service) . ' is stopped...');
                }
            }

            return CDevSuite::info($this->cli->run('service ' . $this->getRealService($service)));
        }
    }

    /**
     * Status of the given services.
     *
     * @param mixed $service Service to get status from
     *
     * @return void
     */
    public function status($service) {
        return $this->cli->run('service ' . $this->getRealService($service) . ' status');
    }

    /**
     * Check if service is disabled.
     *
     * @param mixed $service Service name
     *
     * @return boolean
     */
    public function disabled($service) {
        $service = $this->getRealService($service);

        return (strpos(trim($this->cli->run("systemctl is-enabled {$service}")), 'enabled')) === false;
    }

    /**
     * Disable services.
     *
     * @param mixed $services Service or services to disable
     *
     * @return void
     */
    public function disable($services) {
        if ($this->_hasSystemd()) {
            $services = is_array($services) ? $services : func_get_args();

            foreach ($services as $service) {
                try {
                    $service = $this->getRealService($service);

                    if (!$this->disabled($service)) {
                        $this->cli->quietly('sudo systemctl disable ' . $service);
                        CDevSuite::info(ucfirst($service) . ' has been disabled');
                    }

                    CDevSuite::info(ucfirst($service) . ' was already disabled');
                } catch (DomainException $e) {
                    CDevSuite::warning(ucfirst($service) . ' not available.');
                }
            }
        } else {
            $services = is_array($services) ? $services : func_get_args();

            foreach ($services as $service) {
                try {
                    $service = $this->getRealService($service);
                    $this->cli->quietly("sudo chmod -x /etc/init.d/{$service}");
                    $this->cli->quietly("sudo update-rc.d $service defaults");
                } catch (DomainException $e) {
                    CDevSuite::warning(ucfirst($service) . ' not available.');
                }
            }
        }
    }

    /**
     * Enable services.
     *
     * @param mixed $services Service or services to enable
     *
     * @return void
     */
    public function enable($services) {
        if ($this->_hasSystemd()) {
            $services = is_array($services) ? $services : func_get_args();

            foreach ($services as $service) {
                try {
                    $service = $this->getRealService($service);

                    if ($this->disabled($service)) {
                        $this->cli->quietly('sudo systemctl enable ' . $service);
                        CDevSuite::info(ucfirst($service) . ' has been enabled');

                        return true;
                    }

                    CDevSuite::info(ucfirst($service) . ' was already enabled');

                    return true;
                } catch (DomainException $e) {
                    CDevSuite::warning(ucfirst($service) . ' not available.');

                    return false;
                }
            }
        } else {
            $services = is_array($services) ? $services : func_get_args();

            foreach ($services as $service) {
                try {
                    $service = $this->getRealService($service);
                    $this->cli->quietly("sudo update-rc.d $service defaults");
                    CDevSuite::info(ucfirst($service) . ' has been enabled');

                    return true;
                } catch (DomainException $e) {
                    CDevSuite::warning(ucfirst($service) . ' not available.');

                    return false;
                }
            }
        }
    }

    /**
     * Determine if service manager is available on the system.
     *
     * @return bool
     */
    public function isAvailable() {
        try {
            $output = $this->cli->run(
                    'which service',
                    function ($exitCode, $output) {
                throw new DomainException('Service not available');
            }
            );

            return $output != '';
        } catch (DomainException $e) {
            return false;
        }
    }

    /**
     * Determine real service name
     *
     * @param mixed $service Service name
     *
     * @return string
     */
    public function getRealService($service) {
        return c::collect($service)->first(
                        function ($service) {
                    return !strpos(
                                    $this->cli->run('service ' . $service . ' status'),
                                    'not-found'
                    );
                },
                        function () {
                    throw new DomainException("Unable to determine service name.");
                }
        );
    }

    /**
     * Determine if systemd is available on the system.
     *
     * @return bool
     */
    private function _hasSystemd() {
        try {
            $this->cli->run(
                    'which systemctl',
                    function ($exitCode, $output) {
                throw new DomainException('Systemd not available');
            }
            );

            return true;
        } catch (DomainException $e) {
            return false;
        }
    }

    /**
     * Install DevSuite DNS services.
     *
     * @param Filesystem $files Filesystem object
     *
     * @return void
     */
    public function installDevSuiteDns($files) {
        CDevSuite::info("Installing devsuite DNS service...");

        $servicePath = '/etc/init.d/devsuite-dns';
        $serviceFile = CDevSuite::stubsPath() . 'init/sysvinit';
        $hasSystemd = $this->_hasSystemd();

        if ($hasSystemd) {
            $servicePath = '/etc/systemd/system/devsuite-dns.service';
            $serviceFile = CDevSuite::stubsPath() . 'init/systemd';
        }

        $files->copyAsRoot($serviceFile,$servicePath);

        if (!$hasSystemd) {
            $this->cli->run("sudo chmod +x $servicePath");
        }

        $this->enable('devsuite-dns');
    }

}
