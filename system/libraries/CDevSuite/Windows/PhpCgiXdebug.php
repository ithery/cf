<?php

class CDevSuite_Windows_PhpCgiXdebug extends CDevSuite_Windows_PhpCgi {
    const PORT = 9002;

    /**
     * @inheritDoc
     */
    public function __construct() {
        parent::__construct();
        $winswFactory = new CDevSuite_Windows_WinSWFactory();
        $this->winsw = $winswFactory->make('phpcgixdebugservice');
    }

    /**
     * Install and configure PHP CGI service.
     *
     * @return void
     */
    public function install() {
        CDevSuite::info('Installing PHP-CGI Xdebug service...');

        $this->installService();
    }

    /**
     * Install the Windows service.
     *
     * @return void
     */
    public function installService() {
        if ($this->winsw->installed()) {
            $this->winsw->uninstall();
        }

        $this->winsw->install([
            'PHP_PATH' => $this->findPhpPath(),
            'PHP_XDEBUG_PORT' => $this->configuration->get('php_xdebug_port', CDevSuite_Windows_PhpCgiXdebug::PORT),
        ]);

        $this->winsw->restart();
    }
}
