<?php

trait CBroadcast_Trait_UsePusherChannelConventions {
    /**
     * Return true if the channel is protected by authentication.
     *
     * @param string $channel
     *
     * @return bool
     */
    public function isGuardedChannel($channel) {
        return cstr::startsWith($channel, ['private-', 'presence-']);
    }

    /**
     * Remove prefix from channel name.
     *
     * @param string $channel
     *
     * @return string
     */
    public function normalizeChannelName($channel) {
        foreach (['private-encrypted-', 'private-', 'presence-'] as $prefix) {
            if (cstr::startsWith($channel, $prefix)) {
                return cstr::replaceFirst($prefix, '', $channel);
            }
        }

        return $channel;
    }
}
