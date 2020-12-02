<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CConsole_Command_App_AppPresetCommand extends CConsole_Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create {code} {--domain=?} {--prefix=?}';

    public function handle() {

        $appCode = $this->argument('code');
        $prefix = $this->option('prefix');

        $this->ensureAppDirectoryExists($appCode);
        $this->buildBootstrapFiles($appCode);
        $this->buildControllers($appCode);
    }

    public function buildControllers($appCode) {
        $dir = $this->appDefaultPath($appCode).'controllers'.DS;
        if(!CFile::isDirectory($dir)) {
            CFile::makeDirectory($dir);
        }
        $homeController = $dir.'home.php';
        if(!CFile::exists($homeController)) {
            
        }
    }

    public function buildBootstrapFiles($appCode) {
        $path = $this->appPath($appCode) . 'bootstrap.php';
        $content = CF::FILE_SECURITY . PHP_EOL . PHP_EOL;
        if (!CFile::exists($path)) {
            CFile::put($path, $content);
        }
    }

    /**
     * 
     * @param string $appCode
     */
    public function ensureAppDirectoryExists($appCode) {
        $appPath = $this->appPath($appCode);
        if (!CFile::isDirectory($appPath)) {
            CFile::makeDirectory($path);
        }
        $appDefaultPath = $this->appDefaultPath($appCode);
        if (!CFile::isDirectory($appDefaultPath)) {
            CFile::makeDirectory($appDefaultPath);
        }
    }

    /**
     * get application base directory path
     * @param string $appCode
     * @return string
     */
    public function appPath($appCode) {
        return DOCROOT . 'application' . DS . $appCode . DS;
    }

    /**
     * get application default folder path
     * 
     * @param string $appCode
     * @return string
     */
    public function appDefaultPath($appCode) {
        return DOCROOT . 'application' . DS . $appCode . 'default' . DS;
    }

}
