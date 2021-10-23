<?php

trait CException_Concern_HasContextTrait {
    /**
     * @var null|string
     */
    private $messageLevel;

    /**
     * @var null|string
     */
    private $stage;

    /**
     * @var array
     */
    private $userProvidedContext = [];

    /**
     * @param null|string $stage
     *
     * @return $this
     */
    public function stage($stage) {
        $this->stage = $stage;

        return $this;
    }

    /**
     * @param null|string $messageLevel
     *
     * @return void
     */
    public function messageLevel($messageLevel) {
        $this->messageLevel = $messageLevel;

        return $this;
    }

    /**
     * @param string $groupName
     * @param array  $default
     *
     * @return array
     */
    public function getGroup($groupName = 'context', $default = []) {
        return $this->userProvidedContext[$groupName] ?? $default;
    }

    public function context($key, $value) {
        return $this->group('context', [$key => $value]);
    }

    /**
     * @param string $groupName
     * @param array  $properties
     *
     * @return $this
     */
    public function group($groupName, array $properties) {
        $group = $this->userProvidedContext[$groupName] ?? [];

        $this->userProvidedContext[$groupName] = carr::arrayMergeRecursiveDistinct(
            $group,
            $properties
        );

        return $this;
    }
}
