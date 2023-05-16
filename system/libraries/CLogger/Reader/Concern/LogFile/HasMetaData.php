<?php

trait CLogger_Reader_Concern_LogFile_HasMetadata {
    /**
     * @var array
     */
    protected $metadata = [];

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return void
     */
    public function setMetadata($attribute, $value) {
        $this->metadata[$attribute] = $value;
    }

    /**
     * @param null|string $attribute
     * @param mixed       $default
     *
     * @return mixed
     */
    public function getMetadata($attribute = null, $default = null) {
        if (!isset($this->metadata)) {
            $this->loadMetadata();
        }

        if (isset($attribute)) {
            return $this->metadata[$attribute] ?? $default;
        }

        return $this->metadata;
    }

    /**
     * @return void
     */
    public function saveMetadata() {
        $this->saveMetadataToCache($this->metadata);
    }

    /**
     * @return void
     */
    protected function loadMetadata() {
        $this->metadata = $this->loadMetadataFromCache();
    }
}
