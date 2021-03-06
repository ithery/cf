import { PusherChannel } from './PusherChannel';

/**
 * This class represents a Pusher presence channel.
 */
export class PusherPresenceChannel extends PusherChannel {
    /**
     * Register a callback to be called anytime the member list changes.
     */
    here(callback) {
        this.on('pusher:subscription_succeeded', (data) => {
            callback(Object.keys(data.members).map((k) => data.members[k]));
        });

        return this;
    }

    /**
     * Listen for someone joining the channel.
     */
    joining(callback) {
        this.on('pusher:member_added', (member) => {
            callback(member.info);
        });

        return this;
    }

    /**
     * Listen for someone leaving the channel.
     */
    leaving(callback) {
        this.on('pusher:member_removed', (member) => {
            callback(member.info);
        });

        return this;
    }

    /**
     * Trigger client event on the channel.
     */
    whisper(eventName, data) {
        this.pusher.channels.channels[this.name].trigger(`client-${eventName}`, data);

        return this;
    }
}
