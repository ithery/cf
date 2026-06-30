<?php

abstract class CNotification_MessageAbstract implements CNotification_MessageInterface {
    use CTrait_HasOptions;
    use CNotification_Trait_MessageEventTrait;

    /**
     * @var array
     */
    protected $config;

    /**
     * The event dispatcher instance.
     *
     * @var CEvent_DispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param array $config
     * @param array $options
     */
    public function __construct($config, $options) {
        $this->options = $options;
        $this->config = $config;
        $this->dispatcher = CEvent::dispatcher();
    }
}
