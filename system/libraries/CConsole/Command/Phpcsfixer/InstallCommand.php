<?php

/**
 * Description of ComposerCommand.
 *
 * @author Hery
 */
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class CConsole_Command_Phpcsfixer_InstallCommand extends CConsole_Command {
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
    protected $signature = 'php-cs-fixer:install {args?*} {--opts=?*}';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure() {
        $this->ignoreValidationErrors();
    }

    public function handle() {
        $this->downloadPhpcsfixerPharOnBinPath();
        $this->setupPhpcsfixerConfig();
    }

    protected function downloadPhpcsfixerPharOnBinPath() {
        $pharPath = CQC::phpcsfixer()->phpcsfixerPhar();
        if (!file_exists($pharPath)) {
            $this->info('Downloading php-cs-fixer.phar');
            $errCode = 0;
            $errMessage = '';

            try {
                if (!CFile::isDirectory(dirname($pharPath))) {
                    CFile::makeDirectory(dirname($pharPath), 0755, true);
                }
                copy('http://cpanel.ittron.co.id/application/devcloud/default/data/bin/php-cs-fixer/php-cs-fixer.phar', $pharPath);
                $this->info($pharPath . ' downloaded');
            } catch (Exception $ex) {
                $errCode++;
                $errMessage = $ex->getMessage();
            }

            if ($errCode > 0) {
                $this->error($errMessage);

                return false;
            }
        } else {
            $this->info($pharPath . ' is already installed');
        }

        return true;
    }

    protected function setupPhpcsfixerConfig() {
        $configFile = CQC::phpcsfixer()->phpcsfixerConfiguration();
        if (!CFile::exists($configFile)) {
            $stubFile = DOCROOT . '.php-cs-fixer.dist.php';
            if (!$stubFile) {
                $this->error('.php-cs-fixer.dist.php not found on ' . $stubFile);
                exit(1);
            }
            $content = CFile::get($stubFile);
            CFile::put($configFile, $content);
            $this->info('php-cs-fixer configuration ' . basename($configFile) . ' created on ' . $configFile);
        }
    }
}
