<?php
abstract class CBot_Message_AttachmentAbstract implements CBot_Contract_WebAccessInterface {
    /**
     * @var mixed
     */
    protected $payload;

    /**
     * @var array
     */
    protected $extras = [];

    /**
     * Attachment constructor.
     *
     * @param mixed $payload
     */
    public function __construct($payload) {
        $this->payload = $payload;
    }

    /**
     * @return mixed
     */
    public function getPayload() {
        return $this->payload;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return Attachment
     */
    public function addExtras($key, $value) {
        $this->extras[$key] = $value;

        return $this;
    }

    /**
     * @param null|string $key
     *
     * @return array
     */
    public function getExtras($key = null) {
        if (!is_null($key)) {
            return carr::get($this->extras, $key);
        }

        return $this->extras;
    }
}
