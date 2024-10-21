<?php

class CConsole_Command_Docs_ApiGen_InstallCommand extends CConsole_Command {
    use CConsole_Trait_InteractsWithGitIgnoreTrait;

    /**
     * Command line options that should not be gathered dynamically.
     *
     * @var array
     */
    protected $ignoreOptions = [
        '--continue',
        '--pretend',
        '--help',
        '--quiet',
        '--version',
        '--asci',
        '--no-asci',
        '--no-interactions',
        '--verbose',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:apigen:install {args?*} {--opts=?*}';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure() {
        $this->ignoreValidationErrors();
    }

    public function handle() {
        $this->downloadApiGenPharOnBinPath();
        $this->setupApiGenConfig();
        if ($content = $this->addGitIgnore([
            'apigen.neon',
        ], 'Automatically added by docs:apigen:install')
        ) {
            $this->info('automatically add to .gitignore');
            $this->info($content);
        }
    }

    protected function downloadApiGenPharOnBinPath() {
        $pharPath = CDocs::apiGen()->apiGenPhar();
        if (!file_exists($pharPath)) {
            $this->info('Downloading apigen.phar');
            $errCode = 0;
            $errMessage = '';

            try {
                if (!CFile::isDirectory(dirname($pharPath))) {
                    CFile::makeDirectory(dirname($pharPath), 0755, true);
                }
                copy('http://cpanel.ittron.co.id/application/devcloud/default/data/bin/apigen/apigen.phar', $pharPath);
                $this->info($pharPath . ' downloaded');
            } catch (Exception $ex) {
                $errCode++;
                $errMessage = $ex->getMessage();
            }

            if ($errCode > 0) {
                $this->error($errMessage);

                return false;
            }
        }

        return true;
    }

    protected function setupApiGenConfig() {
        $configFile = CDocs::apiGen()->apiGenConfiguration();
        if (!CFile::exists($configFile)) {
            $stubFile = DOCROOT . 'system' . DS . 'stubs' . DS . 'apigen' . DS . 'apigen.neon.stub';
            if (!$stubFile) {
                $this->error('apigen.neon not found on ' . $stubFile);
                exit(1);
            }
            $content = CFile::get($stubFile);
            CFile::put($configFile, $content);
            $this->info('apigen configuration ' . basename($configFile) . ' created on ' . $configFile);
        }
    }
}
