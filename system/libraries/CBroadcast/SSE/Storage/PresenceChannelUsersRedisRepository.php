<?php

class CBroadcast_SSE_Storage_PresenceChannelUsersRedisRepository implements CBroadcast_SSE_Contract_PresenceChannelUsersRepositoryInterface {
    /**
     * @var \CRedis_Connection_PhpRedisConnection|\CRedis_Connection_PredisConnection
     */
    private $db;

    public function __construct() {
        $this->db = CRedis::instance()->connection(CF::config('broadcast.connections.redis.connection'));
    }

    /**
     * @param CAuth_AuthenticatableInterface $user
     *
     * @return string
     */
    protected function userKey(CAuth_AuthenticatableInterface $user) {
        return method_exists($user, 'getAuthIdentifierForBroadcasting')
                ? $user->getAuthIdentifierForBroadcasting()
                : $user->getAuthIdentifier();
    }

    /**
     * @param CAuth_AuthenticatableInterface $user
     * @param string                         $channel
     *
     * @return string
     */
    protected function connectionsKey(CAuth_AuthenticatableInterface $user, $channel) {
        return "presence_channel:{$channel}:user:" . $this->userKey($user);
    }

    /**
     * @param string                         $channel
     * @param CAuth_AuthenticatableInterface $user
     * @param string                         $connectionId
     *
     * @return bool
     */
    public function join($channel, CAuth_AuthenticatableInterface $user, $connectionId) {
        $firstJoin = false;

        $key = $this->connectionsKey($user, $channel);

        $userConnections = c::collect();

        if ((bool) $this->db->exists($key)) {
            /** @var \CCollection $userConnections */
            $userConnections = unserialize($this->db->get($key));
            if ($userConnections->doesntContain($connectionId)) {
                $userConnections->push($connectionId);
            }
        } else {
            $userConnections->push($connectionId);

            $firstJoin = true;
        }

        $this->db->set($key, serialize($userConnections));

        return $firstJoin;
    }

    /**
     * @param string                         $channel
     * @param CAuth_AuthenticatableInterface $user
     * @param string                         $connectionId
     *
     * @return bool
     */
    public function leave($channel, CAuth_AuthenticatableInterface $user, $connectionId) {
        $disconnected = false;

        $key = $this->connectionsKey($user, $channel);

        if ($this->db->exists($key) === 0) {
            return $disconnected;
        }

        /** @var \CCollection $userConnections */
        $userConnections = unserialize($this->db->get($key));

        if ($userConnections->isEmpty() || $userConnections->count() === 1 && $userConnections->contains($connectionId)) {
            $this->db->del($key);

            return true;
        }

        $this->db->set($key, serialize($userConnections->filter(fn ($id) => $id !== $connectionId)));

        return $disconnected;
    }

    /**
     * @param string $channel
     * @param mixed  $user
     *
     * @return void
     */
    public function getUsers($channel, $user) {
        return $user->newModelQuery()->whereIn(
            $user->getAuthIdentifierName(),
            c::collect($this->db->keys("presence_channel:{$channel}:user:*"))->map(fn ($key) => cstr::afterLast($key, ':'))
        )->get();
    }

    // get all users from all channels
    public function getChannels(CAuth_AuthenticatableInterface $user): CCollection {
        return c::collect($this->db->keys('presence_channel:*:user:' . $this->userKey($user)))->map(fn ($key) => cstr::between($key, 'presence_channel:', ':user'));
    }
}
