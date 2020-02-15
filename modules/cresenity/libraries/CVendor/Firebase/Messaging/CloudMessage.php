<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use CVendor_Firebase_Messaging_MessageTarget as MessageTarget;
use CVendor_Firebase_Messaging_Notification as Notification;
use CVendor_Firebase_Messaging_MessageData as MessageData;

class CVendor_Firebase_Messaging_CloudMessage implements CVendor_Firebase_Messaging_MessageInterface {

    /** @var MessageTarget|null */
    private $target;

    /** @var MessageData|null */
    private $data;

    /** @var Notification|null */
    private $notification;

    /** @var AndroidConfig|null */
    private $androidConfig;

    /** @var ApnsConfig|null */
    private $apnsConfig;

    /** @var WebPushConfig|null */
    private $webPushConfig;

    /** @var FcmOptions|null */
    private $fcmOptions;

    private function __construct() {
        
    }

    /**
     * @param string $type One of "condition", "token", "topic"
     *
     * @throws InvalidArgumentException if the target type or value is invalid
     *
     * @return static
     */
    public static function withTarget($type, $value) {
        return self::create()->withChangedTarget($type, $value);
    }

    /**
     * @return static
     */
    public static function create() {
        return new static();
    }

    /**
     * @return static
     */
    public static function fromArray(array $data) {
        $new = new static();

        if (\count(\array_intersect(\array_keys($data), MessageTarget::TYPES)) > 1) {
            throw new InvalidArgument(
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
     * @param string $type One of "condition", "token", "topic"
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
