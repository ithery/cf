<?php

class CConsole_Command_Api_JWTSecretCommand extends CConsole_Command {
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'api:jwt-secret
        {--s|show : Display the key instead of modifying files.}
        {--always-no : Skip generating key if it already exists.}
        {--f|force : Skip confirmation when overwriting an existing key.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the JWTAuth secret key used to sign the tokens';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $key = cstr::random(64);

        if ($this->option('show')) {
            $this->comment($key);

            return;
        }

        $this->displayKey($key);
    }

    /**
     * Display the key.
     *
     * @param string $key
     *
     * @return void
     */
    protected function displayKey($key) {
        $this->info("jwt-auth secret generated [${key}]");
    }
}
