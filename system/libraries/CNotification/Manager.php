<?php

class CNotification_Manager {
    protected $channels;

    protected $vendors;

    protected static $instance;

    protected $messageHandlers;

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

    protected function registerMessageHandler($vendor, $messageHandler) {
        $this->messageHandlers[$vendor] = $messageHandler;
    }

    protected function getDefaultChannelClass($channel) {
        $channelClassMap = [
            'email' => CNotification_Channel_EmailChannel::class,
            'sms' => CNotification_Channel_SmsChannel::class,
            'whatsapp' => CNotification_Channel_WhatsappChannel::class,
            'database' => CNotification_Channel_DatabaseChannel::class,
            'pushnotification' => CNotification_Channel_PushNotificationChannel::class,

        ];
        $channelClass = carr::get($channelClassMap, strtolower($channel));

        return $channelClass;
    }

    public function registerChannel($channelName, $channel) {
        return $this->channels[$channelName] = $channel;
    }

    /**
     * @param string $channel
     * @param array  $config
     *
     * @return \CNotification_ChannelAbstract
     */
    public function channel($channel, $config = null) {
        if (!isset($this->channels[$channel])) {
            $channelClass = $this->getDefaultChannelClass($channel);
            if ($channelClass) {
                $channelObject = new $channelClass($config);
                $this->registerChannel($channel, $channelObject);
            } else {
                throw new Exception('Channel ' . $channel . ' is not available');
            }
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

    protected function getMessageHandlerClass($vendor) {
        $messageClass = carr::get($this->messageHandlers, $vendor);
        if ($messageClass == null) {
            $messageClass = $this->getDefaultMessageHandlerClass($vendor);
        }

        return $messageClass;
    }

    protected function getDefaultMessageHandlerClass($vendor) {
        $classMap = [
            'sendgrid' => CNotification_Message_SendGrid::class,
            'zenziva' => CNotification_Message_Zenziva::class,
            'watzap' => CNotification_Message_Watzap::class,
            'wago' => CNotification_Message_Wago::class,
        ];
        $messageClass = carr::get($classMap, $vendor);
        if ($messageClass == null) {
            $messageClass = 'CNotification_Message_' . ucfirst(cstr::camel($vendor));
        }

        return $messageClass;
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
