<?php

/**
 * @see https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidconfig
 * @see https://firebase.google.com/docs/cloud-messaging/concept-options#setting-the-priority-of-a-message
 */
final class CVendor_Firebase_Messaging_AndroidConfig implements JsonSerializable {
    const PRIORITY_NORMAL = 'normal';

    const PRIORITY_HIGH = 'high';

    /** @var array
     * {
     *      collapse_key?: string,
     *      priority?: 'normal'|'high',
     *      ttl?: string,
     *      restricted_package_name?: string,
     *      data?: array<string, string>,
     *      notification?: array<string, string>,
     *      fcm_options?: array<string, mixed>,
     *      direct_boot_ok?: bool
     * }
     */
    private $config;

    /**
     * @param array{
     *     collapse_key?: string,
     *     priority?: 'normal'|'high',
     *     ttl?: string,
     *     restricted_package_name?: string,
     *     data?: array<string, string>,
     *     notification?: array<string, string>,
     *     fcm_options?: array<string, mixed>,
     *     direct_boot_ok?: bool
     * } $config
     */
    private function __construct(array $config) {
        $this->config = $config;
    }

    public static function getNew() {
        return new self([]);
    }

    /**
     * @param array{
     *     collapse_key?: string,
     *     priority?: 'normal'|'high',
     *     ttl?: string,
     *     restricted_package_name?: string,
     *     data?: array<string, string>,
     *     notification?: array<string, string>,
     *     fcm_options?: array<string, mixed>,
     *     direct_boot_ok?: bool
     * } $config
     */
    public static function fromArray(array $config) {
        return new self($config);
    }

    public function withDefaultSound() {
        return $this->withSound('default');
    }

    /**
     * The sound to play when the device receives the notification. Supports "default" or the filename
     * of a sound resource bundled in the app. Sound files must reside in /res/raw/.
     *
     * @param mixed $sound
     */
    public function withSound($sound) {
        $config = clone $this;
        $config->config['notification'] = isset($config->config['notification']) ? $config->config['notification'] : [];
        $config->config['notification']['sound'] = $sound;

        return $config;
    }

    public function withHighPriority() {
        return $this->withPriority(self::PRIORITY_HIGH);
    }

    public function withNormalPriority() {
        return $this->withPriority(self::PRIORITY_NORMAL);
    }

    public function withPriority($priority) {
        $config = clone $this;
        $config->config['priority'] = $priority;

        return $config;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize() {
        return \array_filter($this->config, static function ($value) {
            return $value !== null && $value !== [];
        });
    }
}
