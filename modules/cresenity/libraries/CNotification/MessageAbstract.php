<?php

abstract class CNotification_MessageAbstract implements CNotification_MessageInterface {
    use CTrait_HasOptions;
    use CNotification_Trait_MessageEventTrait;

    protected $config;

    /**
     * The event dispatcher instance.
     *
     * @var CEvent_DispatcherInterface
     */
    protected $dispatcher;

    public function __construct($config, $options) {
        $this->options = $options;
        $this->config = $config;
        $this->dispatcher = CEvent::dispatcher();
    }
}
