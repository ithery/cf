<?php

use CVendor_Firebase_Messaging_ApnsConfig as ApnsConfig;
use CVendor_Firebase_Messaging_FcmOptions as FcmOptions;
use CVendor_Firebase_Messaging_MessageData as MessageData;
use CVendor_Firebase_Messaging_Notification as Notification;
use CVendor_Firebase_Messaging_AndroidConfig as AndroidConfig;
use CVendor_Firebase_Messaging_MessageTarget as MessageTarget;
use CVendor_Firebase_Messaging_WebPushConfig as WebPushConfig;

class CVendor_Firebase_Messaging_CloudMessage implements CVendor_Firebase_Messaging_MessageInterface {
    /**
     * @var null|MessageTarget
     */
    private $target;

    /**
     * @var null|MessageData
     */
    private $data;

    /**
     * @var null|Notification
     */
    private $notification;

    /**
     * @var null|AndroidConfig
     */
    private $androidConfig;

    /**
     * @var null|ApnsConfig
     */
    private $apnsConfig;

    /**
     * @var null|WebPushConfig
     */
    private $webPushConfig;

    /**
     * @var null|FcmOptions
     */
    private $fcmOptions;

    private function __construct() {
    }

    /**
     * @param string $type  One of "condition", "token", "topic"
     * @param mixed  $value
     *
     * @throws InvalidArgumentException if the target type or value is invalid
     *
     * @return static
     */
    public static function withTarget($type, $value) {
        return self::create()->withChangedTarget($type, $value);
    }

    /**
     * @return self
     */
    public static function new() {
        return new self();
    }

    /**
     * @return static
     */
    public static function create() {
        return new static();
    }

    /**
     * @param array{
     *     token?: string,
     *     topic?: string,
     *     condition?: string,
     *     data?: MessageData|array<string, string>,
     *     notification?: Notification|array{
     *         title?: string,
     *         body?: string,
     *         image?: string
     *     },
     *     android?: array{
     *         collapse_key?: string,
     *         priority?: 'normal'|'high',
     *         ttl?: string,
     *         restricted_package_name?: string,
     *         data?: array<string, string>,
     *         notification?: array<string, string>,
     *         fcm_options?: array<string, mixed>,
     *         direct_boot_ok?: bool
     *     },
     *     apns?: ApnsConfig|array{
     *          headers?: array<string, string>,
     *          payload?: array<string, mixed>,
     *          fcm_options?: array{
     *              analytics_label?: string,
     *              image?: string
     *          }
     *     },
     *     webpush?: WebPushConfig|array{
     *         headers?: array<string, string>,
     *         data?: array<string, string>,
     *         notification?: array<string, mixed>,
     *         fcm_options?: array{
     *             link?: string,
     *             analytics_label?: string
     *         }
     *     },
     *     fcm_options?: FcmOptions|array{
     *         analytics_label?: string
     *     }
     * } $data
     *
     * @return static
     */
    public static function fromArray(array $data) {
        $new = new static();

        if (\count(\array_intersect(\array_keys($data), MessageTarget::TYPES)) > 1) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException(
                'A message can only have one of the following targets: '
                . \implode(', ', MessageTarget::TYPES)
            );
        }

        if ($targetValue = isset($data[MessageTarget::CONDITION]) ? $data[MessageTarget::CONDITION] : null) {
            $new = $new->withChangedTarget(MessageTarget::CONDITION, (string) $targetValue);
        } elseif ($targetValue = isset($data[MessageTarget::TOKEN]) ? $data[MessageTarget::TOKEN] : null) {
            $new = $new->withChangedTarget(MessageTarget::TOKEN, (string) $targetValue);
        } elseif ($targetValue = isset($data[MessageTarget::TOPIC]) ? $data[MessageTarget::TOPIC] : null) {
            $new = $new->withChangedTarget(MessageTarget::TOPIC, (string) $targetValue);
        }

        if (isset($data['data'])) {
            $new = $new->withData($data['data']);
        }

        if (isset($data['notification'])) {
            $new = $new->withNotification(Notification::fromArray($data['notification']));
        }

        if (isset($data['android'])) {
            $new = $new->withAndroidConfig(AndroidConfig::fromArray($data['android']));
        }

        if (isset($data['apns'])) {
            $new = $new->withApnsConfig(ApnsConfig::fromArray($data['apns']));
        }

        if (isset($data['webpush'])) {
            $new = $new->withWebPushConfig(WebPushConfig::fromArray($data['webpush']));
        }

        if (isset($data['fcm_options'])) {
            $new = $new->withFcmOptions(FcmOptions::fromArray($data['fcm_options']));
        }

        return $new;
    }

    /**
     * @param string $type  One of "condition", "token", "topic"
     * @param mixed  $value
     *
     * @throws InvalidArgumentException if the target type or value is invalid
     *
     * @return static
     */
    public function withChangedTarget($type, $value) {
        $new = clone $this;
        $new->target = MessageTarget::with($type, $value);

        return $new;
    }

    /**
     * @param MessageData|array $data
     *
     * @throws InvalidArgumentException
     *
     * @return static
     */
    public function withData($data) {
        $new = clone $this;
        $new->data = $data instanceof MessageData ? $data : MessageData::fromArray($data);

        return $new;
    }

    /**
     * @param Notification|array $notification
     *
     * @throws InvalidArgumentException
     *
     * @return static
     */
    public function withNotification($notification) {
        $new = clone $this;
        $new->notification = $notification instanceof Notification ? $notification : Notification::fromArray($notification);

        return $new;
    }

    /**
     * @param AndroidConfig|array $config
     *
     * @throws InvalidArgumentException
     *
     * @return static
     */
    public function withAndroidConfig($config) {
        $new = clone $this;
        $new->androidConfig = $config instanceof AndroidConfig ? $config : AndroidConfig::fromArray($config);

        return $new;
    }

    /**
     * @param ApnsConfig|array $config
     *
     * @throws InvalidArgumentException
     *
     * @return static
     */
    public function withApnsConfig($config) {
        $new = clone $this;
        $new->apnsConfig = $config instanceof ApnsConfig ? $config : ApnsConfig::fromArray($config);

        return $new;
    }

    /**
     * @param WebPushConfig|array $config
     *
     * @return static
     */
    public function withWebPushConfig($config) {
        $new = clone $this;
        $new->webPushConfig = $config instanceof WebPushConfig ? $config : WebPushConfig::fromArray($config);

        return $new;
    }

    /**
     * @param FcmOptions|array $options
     *
     * @return static
     */
    public function withFcmOptions($options) {
        $new = clone $this;
        $new->fcmOptions = $options instanceof FcmOptions ? $options : FcmOptions::fromArray($options);

        return $new;
    }

    public function hasTarget() {
        return $this->target ? true : false;
    }

    public function jsonSerialize() {
        $data = [
            'data' => $this->data,
            'notification' => $this->notification,
            'android' => $this->androidConfig,
            'apns' => $this->apnsConfig,
            'webpush' => $this->webPushConfig,
            'fcm_options' => $this->fcmOptions,
        ];

        if ($this->target) {
            $data[$this->target->type()] = $this->target->value();
        }

        return \array_filter($data);
    }
}
