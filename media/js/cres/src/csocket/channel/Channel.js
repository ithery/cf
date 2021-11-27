/**
 * This class represents a basic channel.
 */
export class Channel {
    constructor() {
        this.options = null;
    }
    /**
     * Listen for an event on the channel instance.
     */
    listen(event, callback) {
        return this;
    }

    /**
     * Listen for a whisper event on the channel instance.
     */
    listenForWhisper(event, callback) {
        return this.listen('.client-' + event, callback);
    }

    /**
     * Listen for an event on the channel instance.
     */
    notification(callback) {
        return this.listen('.CNotification_Event_BroadcastNotificationCreated', callback);
    }

    /**
     * Stop listening to an event on the channel instance.
     */
    stopListening(event, callback) {
        return this;
    }

    /**
     * Stop listening for a whisper event on the channel instance.
     */
    stopListeningForWhisper(event, callback) {
        return this.stopListening('.client-' + event, callback);
    }

    /**
     * Register a callback to be called anytime a subscription succeeds.
     */
    subscribed(callback) {
        return this;
    }

    /**
     * Register a callback to be called anytime an error occurs.
     */
    error(callback) {
        return this;
    }
}
