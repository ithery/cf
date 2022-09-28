<?php

class CBot_Message_Outgoing_Question implements JsonSerializable, CBot_Contract_WebAccessInterface {
    /**
     * @var array
     */
    protected $actions;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $callback_id;

    /**
     * @var string
     */
    protected $fallback;

    /**
     * @param string $text
     *
     * @return static
     */
    public static function create($text) {
        return new static($text);
    }

    /**
     * @param string $text
     */
    public function __construct($text) {
        $this->text = $text;
        $this->actions = [];
    }

    /**
     * Set the question fallback value.
     *
     * @param string $fallback
     *
     * @return $this
     */
    public function fallback($fallback) {
        $this->fallback = $fallback;

        return $this;
    }

    /**
     * Set the callback id.
     *
     * @param string $callback_id
     *
     * @return $this
     */
    public function callbackId($callback_id) {
        $this->callback_id = $callback_id;

        return $this;
    }

    public function addAction(CBot_Contract_QuestionActionInterface $action) {
        $this->actions[] = $action->toArray();

        return $this;
    }

    /**
     * @param \CBot_Message_Outgoing_Action_Button $button
     *
     * @return $this
     */
    public function addButton(CBot_Message_Outgoing_Action_Button $button) {
        $this->actions[] = $button->toArray();

        return $this;
    }

    /**
     * @param array $buttons
     *
     * @return $this
     */
    public function addButtons(array $buttons) {
        foreach ($buttons as $button) {
            $this->actions[] = $button->toArray();
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        return [
            'text' => $this->text,
            'fallback' => $this->fallback,
            'callback_id' => $this->callback_id,
            'actions' => $this->actions,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function getButtons() {
        return $this->actions;
    }

    /**
     * @return array
     */
    public function getActions() {
        return $this->actions;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Get the instance as a web accessible array.
     * This will be used within the WebDriver.
     *
     * @return array
     */
    public function toWebDriver() {
        return [
            'type' => (count($this->actions) > 0) ? 'actions' : 'text',
            'text' => $this->text,
            'fallback' => $this->fallback,
            'callback_id' => $this->callback_id,
            'actions' => $this->actions,
        ];
    }
}
