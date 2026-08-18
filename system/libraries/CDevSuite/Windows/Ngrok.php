<?php

use Httpful\Request;

/**
 * Description of Ngrok.
 */
class CDevSuite_Windows_Ngrok {
    /**
     * @var CDevSuite_Windows_CommandLine
     */
    protected $cli;

    /**
     * @var array
     */
    protected $tunnelsEndpoints = [
        'http://127.0.0.1:4040/api/tunnels',
        'http://127.0.0.1:4041/api/tunnels',
    ];

    /**
     * Create a new Ngrok instance.
     *
     * @return void
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
    }

    /**
     * Run the given ngrok command.
     *
     * @param string $command
     *
     * @return void
     */
    public function run(string $command) {
        $binPath = realpath(CDevSuite::binPath());
        $ngrok = $binPath . '/ngrok.exe';

        $this->cli->passthru("\"$ngrok\" $command");
    }

    /**
     * Start an ngrok tunnel for the given domain and port.
     *
     * @param string $domain
     * @param int    $port
     * @param array  $options
     *
     * @return void
     */
    public function start(string $domain, int $port, array $options = []) {
        if ($port === 443 && !$this->hasAuthToken()) {
            CDevSuite::output('Forwarding to local port 443 or a local https:// URL is only available after you sign up.
Sign up at: https://ngrok.com/signup
Then use: valet ngrok authtoken my-token');
            exit(1);
        }

        $options = (new CCollection($options))->map(function ($value, $key) {
            return "--$key=$value";
        })->implode(' ');
        $binPath = realpath(CDevSuite::binPath());
        $ngrok = $binPath . '/ngrok.exe';

        $this->cli->passthru("start \"$domain\" \"$ngrok\" http $domain:$port $options");
    }

    /**
     * Get the current tunnel URL from the Ngrok API.
     *
     * @param null|string $domain
     *
     * @return string
     */
    public function currentTunnelUrl(?string $domain = null) {
        // wait a second for ngrok to start before attempting to find available tunnels
        // sleep(1);

        foreach ($this->tunnelsEndpoints as $endpoint) {
            $response = c::retry(20, function () use ($endpoint, $domain) {
                $body = Request::get($endpoint)->send()->body;

                if (isset($body->tunnels) && count($body->tunnels) > 0) {
                    return $this->findHttpTunnelUrl($body->tunnels, $domain);
                }
            }, 250);

            if (!empty($response)) {
                return $response;
            }
        }

        throw new DomainException('Tunnel not established.');
    }

    /**
     * Find the HTTP tunnel URL from the list of tunnels.
     *
     * @param array       $tunnels
     * @param null|string $domain
     *
     * @return null|void|string
     */
    public function findHttpTunnelUrl(array $tunnels, ?string $domain = null) {
        // If there are active tunnels on the Ngrok instance we will spin through them and
        // find the one responding on HTTP. Each tunnel has an HTTP and a HTTPS address
        // but for local dev purposes we just desire the plain HTTP URL endpoint.
        foreach ($tunnels as $tunnel) {
            if ($tunnel->proto === 'http' && strpos($tunnel->config->addr, $domain)) {
                return $tunnel->public_url;
            }
        }
    }

    /**
     * Determine if an ngrok auth token has been configured.
     *
     * @return bool
     */
    protected function hasAuthToken(): bool {
        return file_exists($_SERVER['HOME'] . '/.ngrok2/ngrok.yml');
    }
}
