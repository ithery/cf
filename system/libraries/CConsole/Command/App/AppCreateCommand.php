<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CConsole_Command_App_AppCreateCommand extends CConsole_Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create {code} {--domain=} {--prefix=} {--title=}';
    protected $allOptions;

    public function handle() {
        CConsole::devSuiteRequired($this);

        $appCode = $this->argument('code');

        $allOptions = $this->allOptions();
        $domain = carr::get($allOptions, 'domain');
        $prefix = carr::get($allOptions, 'prefix');

        if ($domain == null) {
            $defaultDomain = $appCode . '.test';
            if (CF::domainExists($defaultDomain)) {
                $domain = $defaultDomain;
            } else {
                $domain = $this->ask("Please input the domain for create this application", $defaultDomain);
            }
        }
        if (CConsole::domain() != $domain) {
            $this->call("domain:switch", [
                'domain' => $domain,
                '--no-interaction' => carr::get($this->allOptions(), 'no-interaction'),
            ]);
        }
        if (!CF::domainExists($domain)) {
            $this->call("domain:create", [
                'domain' => $domain,
                '--appCode' => $appCode,
                '--no-interaction' => carr::get($this->allOptions(), 'no-interaction'),
            ]);
        }
        if ($prefix == null) {
            $defaultPrefix = cstr::toupper(substr($appCode, 0, 2));
            if ($defaultPrefix == 'CF') {
                $defaultPrefix = 'CC';
            }

            if (strlen(CF::config('app.prefix')) == 0) {
                $prefix = $this->ask("Please input the prefix for this application", $defaultPrefix);
            }
        }


        if ($prefix == 'CF') {
            $this->error('Prefix CF is not available');
            return CConsole::FAILURE_EXIT;
        }



        $this->ensureAppDirectoryExists($appCode);
        $this->buildDefaultConfig($appCode, $prefix);

        CConfig::instance('app')->refresh();
        $prefix = CF::config('app.prefix');

        //reload prefix
        $this->buildMedia($appCode);
        $this->buildTheme($appCode);
        $this->buildLibraries($appCode, $prefix);

        $this->buildViews($appCode);
        $this->buildControllers($appCode);
        $this->buildNav($appCode);
        $this->buildBootstrapFiles($appCode);


        $this->devSuiteLinking($appCode);

        $this->info('Application ' . $appCode . ' created sucessfully');
        $this->call('devsuite:open', ['name' => $appCode]);
    }

    public function devSuiteLinking($appCode) {
        $this->call('devsuite:link', ['name' => $appCode]);
    }

    public function buildBootstrapFiles($appCode) {
        $bootstrapFile = $this->appPath($appCode) . 'bootstrap.php';
        $stubFile = CF::findFile('stubs', 'bootstrap', true, 'stub');
        if (!$stubFile) {
            $this->error('bootstrap stub not found');
            exit(1);
        }
        $content = CFile::get($stubFile);
        $content = str_replace('{theme}', $appCode, $content);
        CFile::put($bootstrapFile, $content);
        $this->info('Bootstrap ' . basename($bootstrapFile) . ' created on ' . $bootstrapFile);
    }

    public function buildViews($appCode) {
        $viewsDir = $this->appDefaultPath($appCode) . 'views' . DS;
        $this->ensureDirectoryExists($viewsDir);
    }

    public function buildLibraries($appCode, $prefix) {
        $librariesDir = $this->appDefaultPath($appCode) . 'libraries' . DS;
        $this->ensureDirectoryExists($librariesDir);

        $baseFile = $librariesDir . $prefix . EXT;
        if (!CFile::exists($baseFile)) {

            $stubFile = CF::findFile('stubs', 'libraries/base/base', true, 'stub');
            if (!$stubFile) {
                $this->error('base stub not found');
                exit(1);
            }
            $content = CFile::get($stubFile);
            $content = str_replace('{prefix}', $prefix, $content);
            CFile::put($baseFile, $content);
            $this->info('Libraries ' . basename($baseFile) . ' created on ' . $baseFile);
        }
        //create base model
        $baseModelFile = $librariesDir . $prefix . 'Model' . EXT;
        $baseModelDir = $librariesDir . $prefix . 'Model';
        $this->ensureDirectoryExists($baseModelDir);
        if (!CFile::exists($baseModelFile)) {

            $stubFile = CF::findFile('stubs', 'libraries/base/model', true, 'stub');
            if (!$stubFile) {
                $this->error('base model stub not found');
                exit(1);
            }
            $content = CFile::get($stubFile);
            $content = str_replace('{prefix}', $prefix, $content);
            CFile::put($baseModelFile, $content);
            $this->info('Libraries ' . basename($baseModelFile) . ' created on ' . $baseModelFile);
        }



        //create base utils
        $baseUtilsFile = $librariesDir . $prefix . 'Utils' . EXT;
        if (!CFile::exists($baseUtilsFile)) {

            $stubFile = CF::findFile('stubs', 'libraries/base/utils', true, 'stub');
            if (!$stubFile) {
                $this->error('base utils stub not found');
                exit(1);
            }
            $content = CFile::get($stubFile);
            $content = str_replace('{prefix}', $prefix, $content);
            CFile::put($baseUtilsFile, $content);
            $this->info('Libraries ' . basename($baseUtilsFile) . ' created on ' . $baseUtilsFile);
        }

        //create base controllers
        $baseControllerFile = $librariesDir . $prefix . 'Controller' . EXT;
        $baseControllerDir = $librariesDir . $prefix . 'Controller';
        $this->ensureDirectoryExists($baseControllerDir);
        if (!CFile::exists($baseControllerFile)) {

            $stubFile = CF::findFile('stubs', 'libraries/base/controller', true, 'stub');
            if (!$stubFile) {
                $this->error('base controller stub not found');
                exit(1);
            }
            $content = CFile::get($stubFile);
            $content = str_replace('{prefix}', $prefix, $content);
            CFile::put($baseControllerFile, $content);
            $this->info('Libraries ' . basename($baseControllerFile) . ' created on ' . $baseControllerFile);
        }
    }

    public function buildTheme($appCode) {
        $this->call('make:theme', [
            'theme' => $appCode,
            '--no-interaction' => carr::get($this->allOptions(), 'no-interaction'),
        ]);
    }

    public function buildMedia($appCode) {
        $mediaDir = $this->appDefaultPath($appCode) . 'media' . DS;
        $jsDir = $mediaDir . 'js' . DS;
        $cssDir = $mediaDir . 'css' . DS;
        $imgDir = $mediaDir . 'img' . DS;

        $this->ensureDirectoryExists($mediaDir);
        $this->ensureDirectoryExists($jsDir);
        $this->ensureDirectoryExists($cssDir);
        $this->ensureDirectoryExists($imgDir);
    }

    public function buildNav($appCode) {

        $this->call('make:nav', [
            'nav' => 'nav',
            '--no-interaction' => carr::get($this->allOptions(), 'no-interaction'),
        ]);
    }

    public function buildControllers($appCode) {
        $this->call('make:controller', [
            'controller' => 'home',
            '--no-interaction' => carr::get($this->allOptions(), 'no-interaction'),
        ]);
    }

    public function buildDefaultConfig($appCode, $prefix) {


        $valueOptions = [
            'prefix' => $prefix,
        ];
        $this->call('make:config', [
            'config' => 'app',
            '--value' => json_encode($valueOptions),
            '--no-interaction' => carr::get($this->allOptions(), 'no-interaction'),
        ]);
    }

    /**
     * 
     * @param string $appCode
     */
    public function ensureAppDirectoryExists($appCode) {
        $appPath = $this->appPath($appCode);
        $this->ensureDirectoryExists($appPath);
        $appDefaultPath = $this->appDefaultPath($appCode);
        $this->ensureDirectoryExists($appDefaultPath);
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
        return DOCROOT . 'application' . DS . $appCode . DS . 'default' . DS;
    }

    public function allOptions() {
        if ($this->allOptions == null) {
            $this->allOptions = $this->option();
        }
        return $this->allOptions;
    }

    protected function ensureDirectoryExists($directory) {
        if (!CFile::isDirectory($directory)) {
            CFile::makeDirectory($directory);
            $this->info("Directory $directory created");
        }
    }

}
