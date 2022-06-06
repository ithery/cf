<?php

use phpseclib3\Crypt\RSA;

class CConsole_Command_Api_OAuth_KeyCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:oauth:key
                                      {--force : Overwrite keys they already exist}
                                      {--length=4096 : The length of the private key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the encryption keys for API authentication';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        list($publicKey, $privateKey) = [DOCROOT . 'oauth-public.key', DOCROOT . 'oauth-private.key'];

        if ((file_exists($publicKey) || file_exists($privateKey)) && !$this->option('force')) {
            $this->error('Encryption keys already exist. Use the --force option to overwrite them.');

            return 1;
        } else {
            $key = RSA::createKey($this->input ? (int) $this->option('length') : 4096);

            file_put_contents($publicKey, (string) $key->getPublicKey());
            file_put_contents($privateKey, (string) $key);

            $this->info('Encryption keys generated successfully.');
        }

        return 0;
    }
}
