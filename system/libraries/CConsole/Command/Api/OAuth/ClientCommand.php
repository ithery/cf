<?php

class CConsole_Command_Api_OAuth_ClientCommand extends CConsole_Command {
    protected $oauth;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:oauth:client
            {--group= : Api Group}
            {--personal : Create a personal access token client}
            {--password : Create a password grant client}
            {--client : Create a client credentials grant client}
            {--name= : The name of the client}
            {--provider= : The name of the user provider}
            {--redirect_uri= : The URI to redirect to after authorization }
            {--user_id= : The user ID the client should be assigned to }
            {--public : Create a public client (Auth code grant type only) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a client for issuing access tokens';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $group = $this->option('group') ?: 'api';
        $this->oauth = CApi::oauth($group);
        $clients = $this->oauth->clientRepository();
        if ($this->option('personal')) {
            $this->createPersonalClient($clients);
        } elseif ($this->option('password')) {
            $this->createPasswordClient($clients);
        } elseif ($this->option('client')) {
            $this->createClientCredentialsClient($clients);
        } else {
            $this->createAuthCodeClient($clients);
        }
    }

    /**
     * Create a new personal access client.
     *
     * @param \CApi_OAuth_ClientRepository $clients
     *
     * @return void
     */
    protected function createPersonalClient(CApi_OAuth_ClientRepository $clients) {
        $name = $this->option('name') ?: $this->ask(
            'What should we name the personal access client?',
            CF::config('app.name') . ' Personal Access Client'
        );

        $client = $clients->createPersonalAccessClient(
            null,
            null,
            null,
            $name,
            'http://localhost'
        );

        $this->info('Personal access client created successfully.');

        $this->outputClientDetails($client);
    }

    /**
     * Create a new password grant client.
     *
     * @param \CApi_OAuth_ClientRepository $clients
     *
     * @return void
     */
    protected function createPasswordClient(CApi_OAuth_ClientRepository $clients) {
        $name = $this->option('name') ?: $this->ask(
            'What should we name the password grant client?',
            CF::config('app.name') . ' Password Grant Client'
        );

        $providers = array_keys(CF::config('auth.providers'));

        $provider = $this->option('provider') ?: $this->choice(
            'Which user provider should this client use to retrieve users?',
            $providers,
            in_array('users', $providers) ? 'users' : null
        );

        $client = $clients->createPasswordGrantClient(
            null,
            $name,
            'http://localhost',
            $provider
        );

        $this->info('Password grant client created successfully.');

        $this->outputClientDetails($client);
    }

    /**
     * Create a client credentials grant client.
     *
     * @param \CApi_OAuth_ClientRepository $clients
     *
     * @return void
     */
    protected function createClientCredentialsClient(CApi_OAuth_ClientRepository $clients) {
        $name = $this->option('name') ?: $this->ask(
            'What should we name the client?',
            CF::config('app.name') . ' ClientCredentials Grant Client'
        );

        $client = $clients->create(
            null,
            null,
            null,
            $name,
            ''
        );

        $this->info('New client created successfully.');

        $this->outputClientDetails($client);
    }

    /**
     * Create a authorization code client.
     *
     * @param \CApi_OAuth_ClientRepository $clients
     *
     * @return void
     */
    protected function createAuthCodeClient(CApi_OAuth_ClientRepository $clients) {
        $userId = $this->option('user_id') ?: $this->ask(
            'Which user ID should the client be assigned to?'
        );

        $name = $this->option('name') ?: $this->ask(
            'What should we name the client?'
        );

        $redirect = $this->option('redirect_uri') ?: $this->ask(
            'Where should we redirect the request after authorization?',
            c::url('/auth/callback')
        );

        $client = $clients->create(
            $userId,
            $name,
            $redirect,
            null,
            false,
            false,
            !$this->option('public')
        );

        $this->info('New client created successfully.');

        $this->outputClientDetails($client);
    }

    /**
     * Output the client's ID and secret key.
     *
     * @param \CApi_OAuth_Model_OAuthClient $client
     *
     * @return void
     */
    protected function outputClientDetails(CApi_OAuth_Model_OAuthClient $client) {
        if ($this->oauth->hashesClientSecrets) {
            $this->line('<comment>Here is your new client secret. This is the only time it will be shown so don\'t lose it!</comment>');
            $this->line('');
        }

        $this->line('<comment>Client ID:</comment> ' . $client->client_id);
        $this->line('<comment>Client secret:</comment> ' . $client->plainSecret);
    }
}
