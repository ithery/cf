<?php

interface CBroadcast_SSE_Contract_PresenceChannelUsersRepositoryInterface {
    public function join($channel, CAuth_AuthenticatableInterface $user, $connectionId);

    public function leave($channel, CAuth_AuthenticatableInterface $user, $connectionId);

    public function getUsers($channel, $user);

    public function getChannels(CAuth_AuthenticatableInterface $user);
}
