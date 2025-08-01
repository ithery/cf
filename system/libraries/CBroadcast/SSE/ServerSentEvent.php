<?php

class CBroadcast_SSE_ServerSentEvent implements Stringable {
    /**
     * @var string
     */
    private $event;

    /**
     * @var string
     */
    private $data;

    /**
     * @var null|string
     */
    private $id = null;

    /**
     * @var null|int
     */
    private $retry = null;

    public function __construct(
        $event,
        $data,
        $id = null,
        $retry = null
    ) {
        $this->event = $event;
        $this->data = $data;
        $this->id = $id;
        $this->retry = $retry;
    }

    /**
     * @param string $data
     *
     * @return self
     */
    public function setData($data) {
        $this->data = $data;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return self
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * @param int $retry
     *
     * @return self
     */
    public function setRetry($retry) {
        $this->retry = $retry;

        return $this;
    }

    /**
     * @param string $property
     *
     * @return string
     */
    private function propertyString($property) {
        return "{$property}: " . $this->$property . PHP_EOL;
    }

    /**
     * @return string
     */
    public function __toString() {
        $event = $this->propertyString('event');

        if ($this->retry) {
            $event .= $this->propertyString('retry');
        }

        $event .= $this->propertyString('data');

        if ($this->id) {
            $event .= $this->propertyString('id');
        }

        return $event . PHP_EOL . PHP_EOL;
    }

    public function __invoke() {
        echo $this;
        ob_flush();
        flush();
    }

    /**
     * @return void
     */
    public function echo() {
        $this();
    }
}
