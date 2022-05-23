<?php

class CNotification_Manager {
    protected $channels;

    protected $vendors;

    protected static $instance;

    /**
     * @return CNotification_Manager
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CNotification_Manager();
        }

        return static::$instance;
    }

    private function __construct() {
        $this->channels = [];
    }

    public function registerChannel($channel, $config = null) {
        $className = 'CNotification_Channel_' . $channel . 'Channel';
        if ($config != null) {
            return new $className($config);
        }
        if (!isset($this->channels[$channel])) {
            return new $className();
        }

        return $this->channels[$channel];
    }

    /**
     * @param string $channel
     * @param array  $config
     *
     * @return \CNotification_ChannelAbstract
     */
    public function channel($channel, $config = null) {
        if (!isset($this->channels[$channel])) {
            return $this->registerChannel($channel, $config);
        }

        return $this->channels[$channel];
    }

    public function createCustomChannel($channelName, $messageHandler) {
        $channel = new CNotification_Channel_CustomChannel(['channel' => $channelName]);
        $channel->setMessageHandler($messageHandler);

        return $this->channels[$channelName] = $channel;
    }

    /**
     * @param string $vendor
     * @param array  $data
     * @param array  $config
     *
     * @return \CNotification_MessageAbstract
     */
    public function createMessage($vendor, $config = [], $data = []) {
        $vendorClass = $this->toMessageClass($vendor);
        $className = 'CNotification_Message_' . $vendorClass . '';

        return new $className($config, $data);
    }

    /**
     * @param string $vendor
     *
     * @return string
     */
    protected function toMessageClass($vendor) {
        switch ($vendor) {
            case 'sendgrid':
                return 'SendGrid';
            case 'zenziva':
            case 'nexmo':
            default:
                return ucfirst(cstr::camel($vendor));
        }

        return ucfirst(cstr::camel($vendor));
    }

    /**
     * @return string
     */
    public function logNotificationModelName() {
        return CF::config('notification.log_notification_model', CNotification_Model_LogNotification::class);
    }

    /**
     * @return \CModel
     */
    public function createLogNotificationModel() {
        $modelName = $this->logNotificationModelName();

        return new $modelName();
    }
}
