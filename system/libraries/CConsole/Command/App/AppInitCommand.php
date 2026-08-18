<?php

/**
 * Interactive wizard that scaffolds a brand new Cresenity application.
 *
 * Replaces app:create, app:preset and app:preset:admin with a single guided flow.
 */
class CConsole_Command_App_AppInitCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init {--domain=} {--prefix=} {--title=} {--admin}';

    /**
     * @var array
     */
    protected $allOptions;

    /**
     * @return int|void
     */
    public function handle() {
        CConsole::devSuiteRequired($this);

        $appCode = $this->resolveAppCode();
        if ($appCode == null) {
            $this->error('phpcf init must be run from inside an application directory, e.g. application/propmind/');

            return CConsole::FAILURE_EXIT;
        }

        $prompt = c::prompt();

        $prompt->intro('Create a new Cresenity application: ' . $appCode);

        $allOptions = $this->allOptions();

        $domain = carr::get($allOptions, 'domain');
        if ($domain == null) {
            $domain = $appCode . '.test';
        }

        if (!CF::domainExists($domain)) {
            $this->call('domain:create', [
                'domain' => $domain,
                '--appCode' => $appCode,
                '--no-interaction' => carr::get($allOptions, 'no-interaction'),
            ]);
        }
        if (CConsole::domain() != $domain) {
            $this->call('domain:switch', [
                'domain' => $domain,
                '--no-interaction' => carr::get($allOptions, 'no-interaction'),
            ]);
        }
        $this->refreshConfig();

        $title = carr::get($allOptions, 'title');
        if ($title == null) {
            $title = $appCode;
        }

        $prefix = carr::get($allOptions, 'prefix');
        if (strlen(CF::config('app.prefix')) == 0) {
            if ($prefix == null) {
                $defaultPrefix = cstr::toupper(substr($appCode, 0, 2));
                if ($defaultPrefix == 'CF') {
                    $defaultPrefix = 'CC';
                }

                $prefix = $prompt->text(
                    'What is the class prefix for this application?',
                    'e.g. ' . $defaultPrefix,
                    $defaultPrefix,
                    'Prefix is required.',
                    function ($value) {
                        if (cstr::toupper($value) == 'CF') {
                            return 'Prefix CF is reserved for the framework.';
                        }

                        return null;
                    },
                    "Used as the class-name prefix for this app's base libraries."
                );
            }

            if (cstr::toupper($prefix) == 'CF') {
                $this->error('Prefix CF is not available');

                return CConsole::FAILURE_EXIT;
            }
        }

        $withAdmin = (bool) $this->option('admin');
        if (!$withAdmin) {
            $withAdmin = $prompt->confirm('Scaffold an admin preset as well?', false);
        }

        $adminTheme = null;
        if ($withAdmin) {
            $adminTheme = $prompt->text('What theme name should the admin preset use?', '', cstr::tolower($appCode) . '-admin', true);
        }

        $this->ensureAppDirectoryExists($appCode);
        $this->buildDefaultConfig($appCode, $prefix, $title);

        $this->refreshConfig();

        //reload prefix, in case the config already existed and had its own value
        $prefix = CF::config('app.prefix');

        $this->buildMedia($appCode);
        $this->buildTheme($appCode);
        $this->buildLibraries($appCode, $prefix);
        $this->buildViews($appCode);
        $this->buildControllers($appCode);
        $this->buildNav($appCode);
        $this->buildBootstrapFiles($appCode);
        $this->devSuiteLinking($appCode);

        if ($withAdmin) {
            $this->ensureAdminDirectoryExists($appCode, $prefix);
            $this->buildAdminTheme($adminTheme);
            $this->buildAdminLibraries($appCode, $prefix, $adminTheme);
            $this->buildAdminNav($adminTheme);
        }

        $prompt->info('Your application is now available at: http://' . $domain);
        $prompt->outro('Application ' . $appCode . ' created successfully');
    }

    /**
     * Re-scan every config/*.php file on disk (framework + active app) back into the
     * live config repository, so newly written config files are picked up within
     * this same process. CConfig::instance()->refresh() was removed from the
     * framework in 2024 (config is now loaded once at bootstrap), so this is its
     * console-context replacement.
     *
     * @return void
     */
    protected function refreshConfig() {
        CFConfig::loadConfiguration(CConfig::manager()->repository());
    }

    /**
     * @param string $appCode
     *
     * @return void
     */
    public function devSuiteLinking($appCode) {
        $this->call('devsuite:link', ['name' => $appCode]);
    }

    /**
     * @param string $appCode
     *
     * @return void
     */
    public function buildBootstrapFiles($appCode) {
        $bootstrapFile = $this->appPath($appCode) . 'bootstrap.php';
        if (CFile::exists($bootstrapFile) && strlen(trim(CFile::get($bootstrapFile))) > 0) {
            $this->info('Bootstrap ' . basename($bootstrapFile) . ' already created, no changes');

            return;
        }
        $stubFile = CF::findFile('stubs', 'bootstrap', true, 'stub');
        if (!$stubFile) {
            $this->error('bootstrap stub not found');
            exit(CConsole::FAILURE_EXIT);
        }
        $content = CFile::get($stubFile);
        $content = str_replace('{theme}', $appCode, $content);
        CFile::put($bootstrapFile, $content);
        $this->info('Bootstrap ' . basename($bootstrapFile) . ' created on ' . $bootstrapFile);
    }

    /**
     * @param string $appCode
     *
     * @return void
     */
    public function buildViews($appCode) {
        $viewsDir = $this->appDefaultPath($appCode) . 'views' . DS;
        $this->ensureDirectoryExists($viewsDir);
    }

    /**
     * @param string $appCode
     * @param string $prefix
     *
     * @return void
     */
    public function buildLibraries($appCode, $prefix) {
        $librariesDir = $this->appDefaultPath($appCode) . 'libraries' . DS;
        $this->ensureDirectoryExists($librariesDir);

        $baseFile = $librariesDir . $prefix . EXT;
        if (!CFile::exists($baseFile)) {
            $stubFile = CF::findFile('stubs', 'libraries/base/base', true, 'stub');
            if (!$stubFile) {
                $this->error('base stub not found');
                exit(CConsole::FAILURE_EXIT);
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
                exit(CConsole::FAILURE_EXIT);
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
                exit(CConsole::FAILURE_EXIT);
            }
            $content = CFile::get($stubFile);
            $content = str_replace('{prefix}', $prefix, $content);
            CFile::put($baseUtilsFile, $content);
            $this->info('Libraries ' . basename($baseUtilsFile) . ' created on ' . $baseUtilsFile);
        }

        //create base controller
        $baseControllerFile = $librariesDir . $prefix . 'Controller' . EXT;
        $baseControllerDir = $librariesDir . $prefix . 'Controller';
        $this->ensureDirectoryExists($baseControllerDir);
        if (!CFile::exists($baseControllerFile)) {
            $stubFile = CF::findFile('stubs', 'libraries/base/controller', true, 'stub');
            if (!$stubFile) {
                $this->error('base controller stub not found');
                exit(CConsole::FAILURE_EXIT);
            }
            $content = CFile::get($stubFile);
            $content = str_replace('{prefix}', $prefix, $content);
            CFile::put($baseControllerFile, $content);
            $this->info('Libraries ' . basename($baseControllerFile) . ' created on ' . $baseControllerFile);
        }
    }

    /**
     * @param string $appCode
     *
     * @return void
     */
    public function buildTheme($appCode) {
        $this->call('make:theme', [
            'theme' => $appCode,
            '--no-interaction' => carr::get($this->allOptions(), 'no-interaction'),
        ]);
    }

    /**
     * @param string $appCode
     *
     * @return void
     */
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

    /**
     * @param string $appCode
     *
     * @return void
     */
    public function buildNav($appCode) {
        $this->call('make:nav', [
            'nav' => 'nav',
            '--no-interaction' => carr::get($this->allOptions(), 'no-interaction'),
        ]);
    }

    /**
     * @param string $appCode
     *
     * @return void
     */
    public function buildControllers($appCode) {
        $this->call('make:controller', [
            'controller' => 'home',
            '--no-interaction' => carr::get($this->allOptions(), 'no-interaction'),
        ]);
    }

    /**
     * @param string $appCode
     * @param string $prefix
     * @param string $title
     *
     * @return void
     */
    public function buildDefaultConfig($appCode, $prefix, $title) {
        $valueOptions = [
            'prefix' => $prefix,
            'title' => $title,
        ];
        $this->call('make:config', [
            'config' => 'app',
            '--value' => json_encode($valueOptions),
            '--no-interaction' => carr::get($this->allOptions(), 'no-interaction'),
        ]);
    }

    /**
     * @param string $appCode
     *
     * @return void
     */
    public function ensureAppDirectoryExists($appCode) {
        $this->ensureDirectoryExists($this->appPath($appCode));
        $this->ensureDirectoryExists($this->appDefaultPath($appCode));
    }

    /**
     * Resolve the application code from the current working directory.
     * phpcf init must be run from inside application/{code}/ (or a subdirectory of it).
     *
     * @return null|string
     */
    protected function resolveAppCode() {
        $appsRoot = c::fixPath(DOCROOT . 'application');
        $cwd = c::fixPath(getcwd());

        if (!cstr::startsWith($cwd, $appsRoot)) {
            return null;
        }

        $relative = trim(substr($cwd, strlen($appsRoot)), DS);
        if (strlen($relative) == 0) {
            return null;
        }

        $segments = explode(DS, $relative);

        return $segments[0];
    }

    /**
     * @param string $theme
     *
     * @return void
     */
    public function buildAdminTheme($theme) {
        $this->call('make:theme', [
            'theme' => $theme,
            '--no-interaction' => carr::get($this->allOptions(), 'no-interaction'),
        ]);
    }

    /**
     * @param string $theme
     *
     * @return void
     */
    public function buildAdminNav($theme) {
        $this->call('make:nav', [
            'nav' => $theme,
            '--no-interaction' => carr::get($this->allOptions(), 'no-interaction'),
        ]);
    }

    /**
     * @param string $appCode
     * @param string $prefix
     * @param string $theme
     *
     * @return void
     */
    public function buildAdminLibraries($appCode, $prefix, $theme) {
        $librariesDir = $this->appDefaultPath($appCode) . 'libraries' . DS;
        $this->ensureDirectoryExists($librariesDir);

        $baseFile = $librariesDir . $prefix . 'Admin' . EXT;
        if (!CFile::exists($baseFile)) {
            $stubFile = CF::findFile('stubs', 'admin/libraries/base/base', true, 'stub');
            if (!$stubFile) {
                $this->error('admin base stub not found');
                exit(CConsole::FAILURE_EXIT);
            }
            $content = CFile::get($stubFile);
            $content = str_replace('{prefix}', $prefix, $content);
            CFile::put($baseFile, $content);
            $this->info('Libraries ' . basename($baseFile) . ' created on ' . $baseFile);
        }

        //create base controller on admin libraries
        $adminLibrariesDir = $this->librariesPath($appCode, $prefix);
        $baseControllerFile = $adminLibrariesDir . 'Controller' . EXT;
        $baseControllerDir = $adminLibrariesDir . 'Controller';
        $this->ensureDirectoryExists($baseControllerDir);
        if (!CFile::exists($baseControllerFile)) {
            $stubFile = CF::findFile('stubs', 'admin/libraries/base/controller', true, 'stub');
            if (!$stubFile) {
                $this->error('admin base controller stub not found');
                exit(CConsole::FAILURE_EXIT);
            }
            $content = CFile::get($stubFile);
            $content = str_replace('{prefix}', $prefix, $content);
            $content = str_replace('{theme}', $theme, $content);
            CFile::put($baseControllerFile, $content);
            $this->info('Libraries ' . basename($baseControllerFile) . ' created on ' . $baseControllerFile);
        }
    }

    /**
     * @param string $appCode
     * @param string $prefix
     *
     * @return void
     */
    public function ensureAdminDirectoryExists($appCode, $prefix) {
        $this->ensureDirectoryExists($this->controllerPath($appCode));
        $this->ensureDirectoryExists($this->librariesPath($appCode, $prefix));
        $this->ensureDirectoryExists($this->viewsPath($appCode));
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

    /**
     * @param string $appCode
     *
     * @return string
     */
    public function controllerPath($appCode) {
        return $this->appDefaultPath($appCode) . 'controllers' . DS . 'admin' . DS;
    }

    /**
     * @param string $appCode
     * @param string $prefix
     *
     * @return string
     */
    public function librariesPath($appCode, $prefix) {
        return $this->appDefaultPath($appCode) . 'libraries' . DS . $prefix . 'Admin' . DS;
    }

    /**
     * @param string $appCode
     *
     * @return string
     */
    public function viewsPath($appCode) {
        return $this->appDefaultPath($appCode) . 'views' . DS . 'admin' . DS;
    }

    /**
     * @return array
     */
    public function allOptions() {
        if ($this->allOptions == null) {
            $this->allOptions = $this->option();
        }

        return $this->allOptions;
    }

    /**
     * @param string $directory
     *
     * @return void
     */
    protected function ensureDirectoryExists($directory) {
        if (!CFile::isDirectory($directory)) {
            CFile::makeDirectory($directory);
            $this->info("Directory $directory created");
        }
    }
}
