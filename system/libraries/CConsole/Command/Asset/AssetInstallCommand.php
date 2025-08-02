<?php

class CConsole_Command_Asset_AssetInstallCommand extends CConsole_Command_AppCommand {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asset:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install asset on public folder';

    private $systemModules = [];

    private $appModules = [];

    private $installedModules = [];

    private $coreModules = [
        'block-ui',
        'moment',
        'datepicker'
    ];

    public function handle() {
        $this->systemModules = require DOCROOT . 'system' . DS . 'data' . DS . 'assets-module.php';
        $clientModulesFile = CF::appPath() . DS . 'default' . DS . 'config' . DS . 'client_modules.php';
        $assetFile = CF::appPath() . DS . 'default' . DS . 'config' . DS . 'assets.php';
        $this->appModules = [];
        if (CFile::exists($clientModulesFile)) {
            $clientModules = require $clientModulesFile;
            $this->appModules = array_merge($this->appModules, $clientModules);
        }
        if (CFile::exists($clientModulesFile)) {
            $assetsConfigData = require $assetFile;
            $clientModules = carr::get($assetsConfigData, 'modules', []);
            if ($clientModules) {
                $this->appModules = array_merge($this->appModules, $clientModules);
            }
        }
        $this->installCresModule();
        $this->installedModules = array_keys($this->appModules);
        $themeDir = CF::appPath() . DS . 'default' . DS . 'themes';
        $themeFiles = CFile::files($themeDir);

        foreach ($themeFiles as $themeFile) {
            $theme = require $themeFile;
            $this->info('Reading ' . basename($themeFile));
            $this->installThemeModules(carr::get($theme, 'modules', carr::get($theme, 'client_modules', [])));
            $this->installThemeJs(carr::get($theme, 'js', []));
            $this->installThemeCss(carr::get($theme, 'css', []));
        }
        $this->installCoreModule();
    }

    public function installCresModule() {
        $source = DOCROOT . 'media' . DS . 'js' . DS . 'cres' . DS . 'dist' . DS . 'cres.js';
        $destination = CF::publicPath() . DS . 'media' . DS . 'js/cres/dist/cres.js';
        $this->copyFile($source, $destination);
        $source = DOCROOT . 'media' . DS . 'js' . DS . 'cres' . DS . 'dist' . DS . 'cres.js.map';
        $destination = CF::publicPath() . DS . 'media' . DS . 'js/cres/dist/cres.js.map';
        $this->copyFile($source, $destination);
        $source = DOCROOT . 'media' . DS . 'js' . DS . 'cres' . DS . 'dist' . DS . 'cres.css';
        $destination = CF::publicPath() . DS . 'media' . DS . 'js/cres/dist/cres.css';
        $this->copyFile($source, $destination);
    }

    public function installCoreModule() {
        foreach ($this->coreModules as $module) {
            if ($module == 'datepicker') {
                if (in_array('bootstrap-4-material', $this->installedModules)) {
                    if (!in_array('bootstrap-4-material-datepicker', $this->installedModules)) {
                        $this->installModule('bootstrap-4-material-datepicker');
                    }
                } elseif (in_array('bootstrap-4', $this->installedModules)) {
                    if (!in_array('bootstrap-4-datepicker', $this->installedModules)) {
                        $this->installModule('bootstrap-4-datepicker');
                    }
                } else {
                    if (!in_array('datepicker', $this->installedModules)) {
                        $this->installModule('datepicker');
                    }
                }
            } else {
                $this->installModule($module);
            }
        }
    }

    public function installThemeModules($modules) {
        foreach ($modules as $module) {
            $this->installModule($module);
        }
    }

    public function installModule($module) {
        if (!array_key_exists($module, $this->appModules)) {
            //we check the system modules
            $this->info('Installing Module ' . $module);
            $moduleToInstall = carr::get($this->systemModules, $module);
            $cssFiles = carr::get($moduleToInstall, 'css');
            $jsFiles = carr::get($moduleToInstall, 'js');
            $files = carr::get($moduleToInstall, 'files');
            $requirements = carr::get($moduleToInstall, 'requirements');
            if ($requirements) {
                foreach ($requirements as $requirement) {
                    $this->installModule($requirement);
                }
            }
            if ($cssFiles) {
                foreach ($cssFiles as $cssFile) {
                    $source = DOCROOT . 'media' . DS . 'css' . DS . $cssFile;
                    $destination = CF::publicPath() . DS . 'media' . DS . 'css' . DS . $cssFile;
                    $this->copyFile($source, $destination);
                }
            }
            if ($jsFiles) {
                foreach ($jsFiles as $jsFile) {
                    $source = DOCROOT . 'media' . DS . 'js' . DS . $jsFile;
                    $destination = CF::publicPath() . DS . 'media' . DS . 'js' . DS . $jsFile;
                    $this->copyFile($source, $destination);
                }
            }
            if ($files) {
                foreach ($files as $relativeFileOrDir) {
                    $fileOrDir = DOCROOT . 'media' . DS . $relativeFileOrDir;
                    if (CFile::isDirectory($fileOrDir)) {
                        $files = CFile::files($fileOrDir);

                        foreach ($files as $file) {
                            $source = $file;
                            $filePath = str_replace(DOCROOT . 'media' . DS, '', $file);

                            $destination = CF::publicPath() . DS . 'media' . DS . $filePath;
                            $this->copyFile($source, $destination);
                        }
                    } else {
                        $source = $fileOrDir;
                        $destination = CF::publicPath() . DS . 'media' . DS . $relativeFileOrDir;
                        $this->copyFile($source, $destination);
                    }
                }
            }
            $this->info('Module ' . $module . ' successfully installed');
            $this->installedModules[] = $module;
        }
    }

    public function installThemeJs($jsFiles) {
        foreach ($jsFiles as $jsFile) {
            $source = DOCROOT . 'media' . DS . 'js' . DS . $jsFile;
            $destination = CF::publicPath() . DS . 'media' . DS . 'js' . DS . $jsFile;
            if (!CFile::exists($destination)) {
                $this->copyFile($source, $destination);
            }
        }
    }

    public function installThemeCss($cssFiles) {
        foreach ($cssFiles as $cssFile) {
            $source = DOCROOT . 'media' . DS . 'css' . DS . $cssFile;
            $destination = CF::publicPath() . DS . 'media' . DS . 'css' . DS . $cssFile;
            if (!CFile::exists($destination)) {
                $this->copyFile($source, $destination);
            }
            if ($cssFile == 'cresenity/cresenity.bs4.css') {
                $source = DOCROOT . 'media' . DS . 'img' . DS . 'glyphicons-halflings.png';
                $destination = CF::publicPath() . DS . 'media' . DS . 'img' . DS . 'glyphicons-halflings.png';
                $this->copyFile($source, $destination);
                $source = DOCROOT . 'media' . DS . 'img' . DS . 'glyphicons-halflings-white.png';
                $destination = CF::publicPath() . DS . 'media' . DS . 'img' . DS . 'glyphicons-halflings-white.png';
                $this->copyFile($source, $destination);
            }
        }
    }

    public function copyFile($source, $destination) {
        $destinationDir = dirname($destination);
        if (!CFile::exists($source)) {
            throw new Exception('File ' . $source . ' doesn\'t exists');
        }
        if (!CFile::isDirectory($destinationDir)) {
            CFile::makeDirectory($destinationDir, 0755, true);
        }
        CFile::copy($source, $destination);
        $this->info('Copied ' . basename($source) . ' to ' . $destination);
    }
}
