<?php

/**
 * Description of ComposerCommand.
 *
 * @author Hery
 */
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class CConsole_Command_Phpstan_InstallCommand extends CConsole_Command {
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
    protected $signature = 'phpstan:install {args?*} {--opts=?*}';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure() {
        $this->ignoreValidationErrors();
    }

    public function handle() {
        $phpStan = CQC::phpstan();
        $this->downloadPhpstanBinaryOnBinPath();
        $this->downloadPhpstanPharOnBinPath();
        $this->setupPhpstanConfig();
        $this->setupPhpstanBootstrap();
    }

    protected function downloadPhpstanBinaryOnBinPath() {
        $binPath = CQC::phpstan()->phpstanBinary();
        if (!file_exists($binPath)) {
            $this->info('Downloading phpstan');
            $errCode = 0;
            $errMessage = '';

            try {
                if (!CFile::isDirectory(dirname($binPath))) {
                    CFile::makeDirectory(dirname($binPath), 0755, true);
                }
                copy('http://cpanel.ittron.co.id/application/devcloud/default/data/bin/phpstan/phpstan', $binPath);
                $this->info($binPath . ' downloaded');
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

    protected function downloadPhpstanPharOnBinPath() {
        $pharPath = CQC::phpstan()->phpstanPhar();
        if (!file_exists($pharPath)) {
            $this->info('Downloading phpstan.phar');
            $errCode = 0;
            $errMessage = '';

            try {
                if (!CFile::isDirectory(dirname($pharPath))) {
                    CFile::makeDirectory(dirname($pharPath), 0755, true);
                }
                copy('http://cpanel.ittron.co.id/application/devcloud/default/data/bin/phpstan/phpstan.phar', $pharPath);
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

    protected function setupPhpstanConfig() {
        $configFile = CQC::phpstan()->phpstanConfiguration();
        if (!CFile::exists($configFile)) {
            $stubFile = CF::findFile('stubs', 'phpstan.neon', true, 'stub');
            if (!$stubFile) {
                $this->error('phpstan.neon stub not found');
                exit(1);
            }
            $content = CFile::get($stubFile);
            $content = str_replace('{APP_CODE}', CF::appCode(), $content);
            CFile::put($configFile, $content);
            $this->info('phpstan configuration ' . basename($configFile) . ' created on ' . $configFile);
        }
    }

    protected function setupPhpstanBootstrap() {
        $bootstrapFile = CQC::phpstan()->phpstanBootstrap();
        if (!CFile::exists($bootstrapFile)) {
            $stubFile = CF::findFile('stubs', 'phpstan-bootstrap.php', true, 'stub');
            if (!$stubFile) {
                $this->error('phpstan-bootstrap.php stub not found');
                exit(1);
            }
            $content = CFile::get($stubFile);
            $content = str_replace('{APP_CODE}', CF::appCode(), $content);
            CFile::put($bootstrapFile, $content);
            $this->info('phpstan bootstrap ' . basename($bootstrapFile) . ' created on ' . $bootstrapFile);
        }
    }
}
