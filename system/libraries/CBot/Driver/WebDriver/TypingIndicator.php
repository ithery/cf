<?php
class CBot_Driver_WebDriver_TypingIndicator implements CBot_Contract_WebAccessInterface {
    /**
     * @var int
     */
    protected $timeout;

    /**
     * @param float $timeout
     *
     * @return CBot_Driver_WebDriver_TypingIndicator
     */
    public static function create($timeout = 1) {
        return new static($timeout);
    }

    /**
     * TypingIndicator constructor.
     *
     * @param int $timeout
     */
    public function __construct($timeout) {
        $this->timeout = $timeout;
    }

    /**
     * Get the instance as a web accessible array.
     * This will be used within the WebDriver.
     *
     * @return array
     */
    public function toWebDriver() {
        return [
            'type' => 'typing_indicator',
            'timeout' => $this->timeout,
        ];
    }
}
