<?php

class CConsole_Command_KeyGenerateCommand extends CConsole_Command {
    //use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'key:generate
                    {--show : Display the key instead of modifying files}
                    {--force : Force the operation to run when in production}';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var null|string
     *
     * @deprecated
     */
    protected static $defaultName = 'key:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application key';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $key = $this->generateRandomKey();

        return $this->line('<comment>' . $key . '</comment>');

        $this->info('Application key set successfully.');
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateRandomKey() {
        return 'base64:' . base64_encode(
            c::crypt()->generateKey(CF::config('app.cipher'))
        );
    }

    /**
     * Set the application key in the environment file.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key) {
        $currentKey = CF::config('app.key');

        if (strlen($currentKey) !== 0 && (!$this->confirmToProceed())) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($key);

        return true;
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param string $key
     *
     * @return void
     */
    protected function writeNewEnvironmentFileWith($key) {
        // file_put_contents($this->laravel->environmentFilePath(), preg_replace(
        //     $this->keyReplacementPattern(),
        //     'APP_KEY=' . $key,
        //     file_get_contents($this->laravel->environmentFilePath())
        // ));
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern() {
        $escaped = preg_quote('=' . CF::config('app.key'), '/');

        return "/^APP_KEY{$escaped}/m";
    }
}
