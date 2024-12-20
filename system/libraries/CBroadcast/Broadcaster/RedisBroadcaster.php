<?php

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CBroadcast_Broadcaster_RedisBroadcaster extends CBroadcast_BroadcasterAbstract {
    use CBroadcast_Trait_UsePusherChannelConventionsTrait;

    /**
     * The Redis instance.
     *
     * @var \CRedis_FactoryInterface
     */
    protected $redis;

    /**
     * The Redis connection to use for broadcasting.
     *
     * @var ?string
     */
    protected $connection = null;

    /**
     * The Redis key prefix.
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Create a new broadcaster instance.
     *
     * @param \CRedis_FactoryInterface $redis
     * @param null|string              $connection
     * @param string                   $prefix
     *
     * @return void
     */
    public function __construct(CRedis_FactoryInterface $redis, $connection = null, $prefix = '') {
        $this->redis = $redis;
        $this->prefix = $prefix;
        $this->connection = $connection;
    }

    /**
     * Authenticate the incoming request for a given channel.
     *
     * @param \CHTTP_Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     * @return mixed
     */
    public function auth($request) {
        $channelName = $this->normalizeChannelName(
            str_replace($this->prefix, '', $request->channel_name)
        );

        if (empty($request->channel_name)
            || ($this->isGuardedChannel($request->channel_name)
            && !$this->retrieveUser($request, $channelName))
        ) {
            throw new AccessDeniedHttpException();
        }

        return parent::verifyUserCanAccessChannel(
            $request,
            $channelName
        );
    }

    /**
     * Return the valid authentication response.
     *
     * @param \CHTTP_Request $request
     * @param mixed          $result
     *
     * @return mixed
     */
    public function validAuthenticationResponse($request, $result) {
        if (is_bool($result)) {
            return json_encode($result);
        }

        $channelName = $this->normalizeChannelName($request->channel_name);

        $user = $this->retrieveUser($request, $channelName);

        $broadcastIdentifier = method_exists($user, 'getAuthIdentifierForBroadcasting')
                        ? $user->getAuthIdentifierForBroadcasting()
                        : $user->getAuthIdentifier();

        return json_encode(['channel_data' => [
            'user_id' => $broadcastIdentifier,
            'user_info' => $result,
        ]]);
    }

    /**
     * Broadcast the given event.
     *
     * @param array  $channels
     * @param string $event
     * @param array  $payload
     *
     * @return void
     */
    public function broadcast(array $channels, $event, array $payload = []) {
        if (empty($channels)) {
            return;
        }

        $connection = $this->redis->connection($this->connection);

        $payload = json_encode([
            'event' => $event,
            'data' => $payload,
            'socket' => carr::pull($payload, 'socket'),
        ]);

        /** @var CRedis_Connection_PhpRedisConnection $connection */
        $connection->eval(
            $this->broadcastMultipleChannelsScript(),
            0,
            $payload,
            ...$this->formatChannels($channels)
        );
    }

    /**
     * Get the Lua script for broadcasting to multiple channels.
     *
     * ARGV[1] - The payload
     * ARGV[2...] - The channels
     *
     * @return string
     */
    protected function broadcastMultipleChannelsScript() {
        return <<<'LUA'
for i = 2, #ARGV do
  redis.call('publish', ARGV[i], ARGV[1])
end
LUA;
    }

    /**
     * Format the channel array into an array of strings.
     *
     * @param array $channels
     *
     * @return array
     */
    protected function formatChannels(array $channels) {
        return array_map(function ($channel) {
            return $this->prefix . $channel;
        }, parent::formatChannels($channels));
    }
}
