import { NullChannel } from './NullChannel';
/**
 * This class represents a null presence channel.
 */
export class NullPresenceChannel extends NullChannel {
    /**
     * Register a callback to be called anytime the member list changes.
     */
    here(callback) {
        return this;
    }

    /**
     * Listen for someone joining the channel.
     */
    joining(callback) {
        return this;
    }

    /**
     * Listen for someone leaving the channel.
     */
    leaving(callback) {
        return this;
    }

    /**
     * Trigger client event on the channel.
     */
    whisper(eventName, data) {
        return this;
    }
}
