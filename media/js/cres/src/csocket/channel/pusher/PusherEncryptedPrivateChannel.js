import { PusherChannel } from './PusherChannel';

/**
 * This class represents a Pusher private channel.
 */
export class PusherEncryptedPrivateChannel extends PusherChannel {
    /**
     * Trigger client event on the channel.
     */
    whisper(eventName, data) {
        this.pusher.channels.channels[this.name].trigger(`client-${eventName}`, data);

        return this;
    }
}
