import { NullChannel } from './NullChannel';
/**
 * This class represents a null private channel.
 */
export class NullPrivateChannel extends NullChannel {
    /**
     * Trigger client event on the channel.
     */
    whisper(eventName, data) {
        return this;
    }
}
