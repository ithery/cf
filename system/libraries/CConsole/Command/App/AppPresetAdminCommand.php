<?php

class CConsole_Command_App_AppPresetAdminCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:preset:admin';
    protected $allOptions;

    public function handle() {
        CConsole::devSuiteRequired($this);
        CConsole::domainRequired($this);
        CConsole::prefixRequired($this);

        $allOptions = $this->allOptions();
        $theme = carr::get($allOptions, 'theme');

        $domain = CConsole::domain();
        $prefix = CConsole::prefix();

        $appCode = CF::appCode();

        if ($theme == null) {
            $defaultTheme = cstr::tolower($appCode) . '-admin';

            $theme = $this->ask('Please input the theme for admin preset', $defaultTheme);
        }

        $this->ensureAdminDirectoryExists($appCode, $prefix);
        $this->buildTheme($theme);

        $this->buildLibraries($appCode, $prefix, $theme);
        $this->buildNav($theme);

        $this->buildViews($appCode);
        $this->buildControllers($appCode);

        $this->info('Preset Admin ' . $appCode . ' created sucessfully');
    }

    public function buildViews($appCode) {
        $viewsDir = $this->appDefaultPath($appCode) . 'views' . DS;
        $this->ensureDirectoryExists($viewsDir);
    }

    public function buildLibraries($appCode, $prefix, $theme) {
        $librariesDir = $this->appDefaultPath($appCode) . 'libraries' . DS;
        $this->ensureDirectoryExists($librariesDir);

        $baseFile = $librariesDir . $prefix . 'Admin' . EXT;
        if (!CFile::exists($baseFile)) {
            $stubFile = CF::findFile('stubs', 'libraries/base/admin/base', true, 'stub');
            if (!$stubFile) {
                $this->error('base stub not found');
                exit(1);
            }
            $content = CFile::get($stubFile);
            $content = str_replace('{prefix}', $prefix, $content);
            CFile::put($baseFile, $content);
            $this->info('Libraries ' . basename($baseFile) . ' created on ' . $baseFile);
        }

        //create base controllers on admin libraries
        $librariesDir = $this->librariesPath($appCode, $prefix);
        $baseControllerFile = $librariesDir . 'Controller' . EXT;
        $baseControllerDir = $librariesDir . 'Controller';
        $this->ensureDirectoryExists($baseControllerDir);
        if (!CFile::exists($baseControllerFile)) {
            $stubFile = CF::findFile('stubs', 'libraries/base/admin/controller', true, 'stub');
            if (!$stubFile) {
                $this->error('base controller stub not found');
                exit(1);
            }
            $content = CFile::get($stubFile);
            $content = str_replace('{prefix}', $prefix, $content);
            $content = str_replace('{theme}', $theme, $content);
            CFile::put($baseControllerFile, $content);
            $this->info('Libraries ' . basename($baseControllerFile) . ' created on ' . $baseControllerFile);
        }
    }

    public function buildTheme($theme) {
        $this->call('make:theme', [
            'theme' => $theme,
            '--no-interaction' => carr::get($this->allOptions(), 'no-interaction'),
        ]);
    }

    public function buildNav($theme) {
        $this->call('make:nav', [
            'nav' => $theme,
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
     * @param string $appCode
     * @param mixed  $prefix
     */
    public function ensureAdminDirectoryExists($appCode, $prefix) {
        $this->ensureDirectoryExists($this->controllerPath($appCode));
        $this->ensureDirectoryExists($this->librariesPath($appCode, $prefix));
        $this->ensureDirectoryExists($this->viewsPath($appCode, $prefix));
    }

    /**
     * Get application base directory path
     *
     * @param string $appCode
     *
     * @return string
     */
    public function appPath($appCode) {
        return DOCROOT . 'application' . DS . $appCode . DS;
    }

    /**
     * Get application default folder path
     *
     * @param string $appCode
     *
     * @return string
     */
    public function appDefaultPath($appCode) {
        return $this->appPath($appCode) . 'default' . DS;
    }

    public function controllerPath($appCode) {
        return $this->appDefaultPath($appCode) . 'controllers' . DS . 'admin' . DS;
    }

    public function librariesPath($appCode, $prefix) {
        return $this->appDefaultPath($appCode) . 'libraries' . DS . $prefix . 'Admin' . DS;
    }

    public function viewsPath($appCode, $prefix) {
        return $this->appDefaultPath($appCode) . 'views' . DS . 'admin' . DS;
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
