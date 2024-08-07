<?php

/**
 * Description of ComposerCommand.
 *
 * @author Hery
 */
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class CConsole_Command_Phpcs_InstallCommand extends CConsole_Command {
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
    protected $signature = 'phpcs:install {args?*} {--opts=?*}';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure() {
        $this->ignoreValidationErrors();
    }

    public function handle() {
        $phpStan = CQC::phpcs();
        $this->downloadPhpcsPharOnBinPath();
        $this->downloadPhpcbfPharOnBinPath();
        $this->setupPhpcsConfig();
    }

    protected function downloadPhpcsPharOnBinPath() {
        $pharPath = CQC::phpcs()->phpcsPhar();
        if (!file_exists($pharPath)) {
            $this->info('Downloading phpcs.phar');
            $errCode = 0;
            $errMessage = '';

            try {
                if (!CFile::isDirectory(dirname($pharPath))) {
                    CFile::makeDirectory(dirname($pharPath), 0755, true);
                }
                copy('http://cpanel.ittron.co.id/application/devcloud/default/data/bin/phpcs/phpcs.phar', $pharPath);
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

    protected function downloadPhpcbfPharOnBinPath() {
        $pharPath = CQC::phpcs()->phpcbfPhar();
        if (!file_exists($pharPath)) {
            $this->info('Downloading phpcbf.phar');
            $errCode = 0;
            $errMessage = '';

            try {
                if (!CFile::isDirectory(dirname($pharPath))) {
                    CFile::makeDirectory(dirname($pharPath), 0755, true);
                }
                copy('http://cpanel.ittron.co.id/application/devcloud/default/data/bin/phpcs/phpcbf.phar', $pharPath);
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

    protected function setupPhpcsConfig() {
        $configFile = CQC::phpcs()->phpcsConfiguration();
        if (!CFile::exists($configFile)) {
            $stubFile = DOCROOT . 'phpcs.xml';
            if (!$stubFile) {
                $this->error('phpcs.xml not found on ' . $stubFile);
                exit(1);
            }
            $content = CFile::get($stubFile);
            CFile::put($configFile, $content);
            $this->info('phpcs configuration ' . basename($configFile) . ' created on ' . $configFile);
        }
    }
}
