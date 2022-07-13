<?php
class CServer_SMTP_Event {
    const TRIGGER_NEW_MAIL = 1000;

    const TRIGGER_NEW_RCPT = 2000;

    const TRIGGER_AUTH_ATTEMPT = 9000;

    /**
     * @var int
     */
    private $trigger;

    /**
     * @var object
     */
    private $object;

    /**
     * @var Closure
     */
    private $function;

    /**
     * @var mixed
     */
    private $returnValue;

    /**
     * @var Client
     */
    private $client;

    /**
     * Event constructor.
     *
     * @param null|int            $trigger
     * @param null|object         $object
     * @param null|Closure|string $function
     */
    public function __construct($trigger = null, $object = null, $function = null) {
        $this->trigger = $trigger;
        $this->object = $object;
        $this->function = $function;
    }

    /**
     * @return null|int
     */
    public function getTrigger() {
        return $this->trigger;
    }

    /**
     * @return mixed
     */
    public function getReturnValue() {
        return $this->returnValue;
    }

    /**
     * @return CServer_SMTP_Client
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * @param CServer_SMTP_Client $client
     * @param array               $args
     *
     * @return mixed
     */
    public function execute(CServer_SMTP_Client $client, array $args = []) {
        $object = $this->object;
        $function = $this->function;

        $this->client = $client;

        array_unshift($args, $this);

        if ($object) {
            $this->returnValue = call_user_func_array([$object, $function], $args);
        } else {
            $this->returnValue = call_user_func_array($function, $args);
        }

        return $this->returnValue;
    }
}
