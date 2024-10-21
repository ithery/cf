<?php

class CConsole_Command_Docs_PhpDoc_InstallCommand extends CConsole_Command {
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
    protected $signature = 'docs:phpdoc:install {args?*} {--opts=?*}';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure() {
        $this->ignoreValidationErrors();
    }

    public function handle() {
        $this->downloadPhpDocumentorPharOnBinPath();
        $this->setupPhpDocumentorConfig();
        if ($content = $this->addGitIgnore([
            'phpdoc.dist.xml',
            '.phpdoc',
            '.phpdoc/*'
        ], 'Automatically added by docs:phpdoc:install')
        ) {
            $this->info('automatically add to .gitignore');
            $this->info($content);
        }
    }

    protected function downloadPhpDocumentorPharOnBinPath() {
        $pharPath = CDocs::phpDocumentor()->phpDocumentorPhar();
        if (!file_exists($pharPath)) {
            $this->info('Downloading phpDocumentor.phar');
            $errCode = 0;
            $errMessage = '';

            try {
                if (!CFile::isDirectory(dirname($pharPath))) {
                    CFile::makeDirectory(dirname($pharPath), 0755, true);
                }
                copy('http://cpanel.ittron.co.id/application/devcloud/default/data/bin/phpDocumentor/phpDocumentor.phar', $pharPath);
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

    protected function setupPhpDocumentorConfig() {
        $configFile = CDocs::phpDocumentor()->phpDocumentorConfiguration();
        if (!CFile::exists($configFile)) {
            $stubFile = DOCROOT . 'system' . DS . 'stubs' . DS . 'phpdoc' . DS . 'phpdoc.dist.xml.stub';
            if (!$stubFile) {
                $this->error('phpdoc.dist.xml not found on ' . $stubFile);
                exit(1);
            }
            $content = CFile::get($stubFile);

            $content = str_replace('##APP_TITLE##', $this->getAppTitle(), $content);
            CFile::put($configFile, $content);
            $this->info('phpdoc configuration ' . basename($configFile) . ' created on ' . $configFile);
        }
    }

    private function getAppTitle() {
        return CF::config('app.title', CF::appCode() ?: 'Cresenity');
    }
}
