<?php

/**
 * A request handler that sends PSR-7-compatible requests with Guzzle 6.
 */
class Aws_Handler_GuzzleV6_GuzzleHandler
{
    /** @var ClientInterface */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(GuzzleHttp_ClientInterface $client = null)
    {
        $this->client = $client ?: new GuzzleHttp_Client();
    }

    /**
     * @param Psr7Request $request
     * @param array       $options
     *
     * @return Promise\Promise
     */
    public function __invoke(GuzzleHttp_Psr7_Request $request, array $options = [])
    {
        $request = $request->withHeader(
            'User-Agent',
            $request->getHeaderLine('User-Agent')
                . ' ' . guzzlehttp_default_user_agent()
        );

        return $this->client->sendAsync($request, $this->parseOptions($options))
            ->otherwise(
                static function (\Exception $e) {
                    $error = [
                        'exception'        => $e,
                        'connection_error' => $e instanceof ConnectException,
                        'response'         => null,
                    ];

                    if ($e instanceof RequestException && $e->getResponse()) {
                        $error['response'] = $e->getResponse();
                    }

                    return new GuzzleHttp_Promise_RejectedPromise($error);
                }
            );
    }

    private function parseOptions(array $options)
    {
        if (isset($options['http_stats_receiver'])) {
            $fn = $options['http_stats_receiver'];
            unset($options['http_stats_receiver']);

            $prev = isset($options['on_stats'])
                ? $options['on_stats']
                : null;

            $options['on_stats'] = static function (
                TransferStats $stats
            ) use ($fn, $prev) {
                if (is_callable($prev)) {
                    $prev($stats);
                }
                $transferStats = ['total_time' => $stats->getTransferTime()];
                $transferStats += $stats->getHandlerStats();
                $fn($transferStats);
            };
        }

        return $options;
    }
}
